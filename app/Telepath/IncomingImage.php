<?php

namespace App\Telepath;

use App\Jobs\UpdateDTicket;
use App\Models\PassDetails\DTicket;
use Illuminate\Support\Facades\Log;
use Telepath\Bot;
use Telepath\Handlers\Message\MessageType;
use Telepath\Telegram\PhotoSize;
use Telepath\Telegram\ReactionTypeEmoji;
use Telepath\Telegram\Update;

class IncomingImage
{
    #[MessageType(MessageType::PHOTO)]
    #[MessageType(MessageType::DOCUMENT)]
    public function incomingImage(Update $update, Bot $bot)
    {
        $sender = $update->message->from;
        /** @var DTicket $ticket */
        $ticket = DTicket::whereTelegramUserId($sender->id)->first();

        if (! $ticket) {
            $bot->sendMessage(
                $sender->id,
                '🚫 Du bist nicht für diesen Service freigeschaltet. Bitte melde dich bei @TiiFuchs.'
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
                Log::warning('mimetype for incoming document: '.var_export($document->mime_type, true));

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

        $bot->setMessageReaction(
            chat_id: $sender->id,
            message_id: $update->message->message_id,
            reaction: [ReactionTypeEmoji::make('👀')]
        );

        dispatch(new UpdateDTicket($ticket, $filename, $update->message));
    }
}
