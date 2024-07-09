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
    #[MessageType(MessageType::DOCUMENT)]
    public function incomingImage(Update $update, Bot $bot)
    {
        $sender = $update->message->from;
        $ticket = DTicket::whereTelegramUserId($sender->id)->first();

        if (!$ticket) {
            $bot->sendMessage(
                $sender->id,
                'Du hast noch kein Deutschlandticket registriert. Bitte melde dich bei @TiiFuchs.'
            );

            return;
        }

        $wasPhoto = false;
        if ($photo = $update->message->photo) {
            $wasPhoto = true;
            /** @var PhotoSize $originalPhoto */
            $originalPhoto = collect($photo)->sortByDesc->file_size->first();
            $file = $bot->getFile($originalPhoto->file_id);
        } elseif ($document = $update->message->document) {
            $mimeType = explode('/', $document->mime_type)[0] ?? null;

            if ($mimeType !== 'image') {
                $bot->sendMessage(
                    chat_id: $sender->id,
                    text: 'Ich kann nur mit Bilddateien etwas anfangen. Bitte sende mir einen Screenshot als Datei.',
                );

                return;
            }

            $file = $bot->getFile($document->file_id);
        }

        $filename = tempnam(storage_path('app/photos'), 'photo_');
        $file->saveTo($filename);

        $success = $ticket->parseScreenshot($filename);

        unlink($filename);

        if (!$success) {
            $text = '⚠️ Ich habe auf dem Screenshot keinen gültigen Code erkennen können.';

            if ($wasPhoto) {
                $text .= "\n" . 'Schick mir den Screenshot doch bitte noch einmal als Datei.';
            }

            $bot->sendMessage(
                chat_id: $sender->id,
                text: $text,
            );

            return;
        }

        $ticket->pass->pushToDevices();

        if ($ticket->pass->devices()->count() === 0) {
            $bot->sendMessage(
                chat_id: $sender->id,
                text: 'Dein Deutschlandticket wurde erstellt.',
                reply_markup: InlineKeyboardMarkup::make([[
                    InlineKeyboardButton::make(
                        'Zu Wallet hinzufügen',
                        url: $ticket->pass->downloadLink(),
                    ),
                ]])
            );

            return;
        }

        $bot->sendMessage(
            chat_id: $sender->id,
            text: 'Dein Deutschlandticket wurde aktualisiert.'
        );
    }
}
