<?php

namespace App\Telepath;

use App\Models\PassDetails\DTicket;
use Telepath\Bot;
use Telepath\Handlers\Message\MessageType;
use Telepath\Laravel\Facades\Telepath;
use Telepath\Telegram\InlineKeyboardButton;
use Telepath\Telegram\InlineKeyboardMarkup;
use Telepath\Telegram\PhotoSize;
use Telepath\Telegram\Update;

class IncomingImage
{
    #[MessageType(MessageType::PHOTO)]
    public function incomingImage(Update $update, Bot $bot)
    {
        $sender = $update->message->from;
        $ticket = DTicket::whereTelegramUserId($sender->id)->first();

        if (! $ticket) {
            $bot->sendMessage(
                $sender->id,
                'Du hast noch kein Deutschlandticket registriert. Bitte melde dich bei @TiiFuchs.'
            );

            return;
        }

        $photo = $update->message->photo;
        /** @var PhotoSize $originalPhoto */
        $originalPhoto = collect($photo)->sortByDesc(fn (PhotoSize $photo) => $photo->file_size)->first();
        $file = $bot->getFile($originalPhoto->file_id);

        $filename = tempnam(storage_path('app/photos'), 'photo_');
        $file->saveTo($filename);

        $barcodeWasNull = $ticket->barcode === null;

        $ticket->parseScreenshot($filename);

        unlink($filename);

        $ticket->pass->pushToDevices();

        if ($barcodeWasNull) {
            Telepath::bot()->sendMessage(
                chat_id: $sender->id,
                text: 'Dein Deutschlandticket wurde erstellt.',
                reply_markup: InlineKeyboardMarkup::make([[
                    InlineKeyboardButton::make(
                        'Zu Wallet hinzufügen',
                        url: $ticket->pass->downloadLink(),
                    ),
                ]])
            );
        } else {
            Telepath::bot()->sendMessage(
                chat_id: $sender->id,
                text: 'Dein Deutschlandticket wurde aktualisiert.'
            );
        }
    }
}
