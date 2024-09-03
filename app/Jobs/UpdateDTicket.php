<?php

namespace App\Jobs;

use App\Exceptions\Uic918\InvalidDataException;
use App\Exceptions\ZXParserException;
use App\Facades\UicTicketParser;
use App\Facades\ZXParser;
use App\Models\PassDetails\DTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telepath\Bot;
use Telepath\Telegram\InlineKeyboardButton;
use Telepath\Telegram\InlineKeyboardMarkup;
use Telepath\Telegram\Message;
use Telepath\Telegram\ReactionTypeEmoji;
use Telepath\Types\Enums\ChatActionType;

class UpdateDTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected readonly DTicket $dTicket,
        protected readonly string $filename,
        protected readonly Message $message,
    ) {}

    protected Bot $bot;

    public function handle(Bot $bot): void
    {
        $this->bot = $bot;

        $this->typing();

        // Parse screenshot
        try {
            $barcode = ZXParser::parse($this->filename);

            $ticket = UicTicketParser::parse($barcode);
        } catch (ZXParserException $e) {
            // No barcode detected
            $this->abortBarcodeNotRecognized();

            return;
        } catch (InvalidDataException $e) {
            // Could not decode ticket
            $this->abortTicketNotSupported();

            return;
        }

        // Ticket is already expired
        $validUntil = $ticket->validUntil();
        if ($validUntil->isPast()) {
            $this->abortTicketExpired();

            return;
        }

        // Ticket is valid in future but there is already a valid ticket in stock, and it's not the last of month
        $validFrom = $ticket->validFrom();
        if (
            $validFrom->isFuture()
            && $this->dTicket->valid_in->isCurrentMonth()
            && ! today()->isLastOfMonth()
        ) {
            $this->abortCurrentTicketMoreRelevant();

            return;
        }

        // Update ticket
        $this->dTicket->update([
            'name' => $ticket->name(),
            'valid_in' => $validFrom,
            'barcode' => $barcode,
        ]);

        // Push to devices
        $this->dTicket->pass->pushToDevices();

        // Remove screenshot
        unlink($this->filename);

        if ($this->dTicket->pass()->devices()->count() === 0) {
            $this->bot->sendMessage(
                chat_id: $this->message->from->id,
                text: "🎫 Dein Deutschlandticket Wallet Pass wurde erstellt.\nBitte öffne den Link in Safari.",
                reply_markup: InlineKeyboardMarkup::make([[
                    InlineKeyboardButton::make(
                        'Zu Wallet hinzufügen',
                        url: $this->dTicket->pass->downloadLink(),
                    ),
                ]])
            );

            return;
        }

        $this->setReactionGood();
        $this->respond('✅ Dein Deutschlandticket Wallet Pass wird automatisch aktualisiert. Du musst nichts weiter tun.');
    }

    protected function abortBarcodeNotRecognized()
    {
        $this->setReactionError();
        $this->respond('⚡ Ich habe auf dem Screenshot keinen Barcode erkennen können.');
    }

    protected function abortTicketNotSupported()
    {
        $this->setReactionError();
        $this->respond('⚡ Dein Ticket wird leider nicht unterstützt.');
    }

    protected function abortTicketExpired()
    {
        $this->setReactionBad();
        $this->respond('⌛ Dieses Ticket ist bereits abgelaufen.');
    }

    protected function abortCurrentTicketMoreRelevant()
    {
        $this->setReactionBad();
        $this->respond('🎫 Dein aktuelles D-Ticket ist noch gültig. Schick mir das neue Ticket am letzten Tag des Monats.');
    }

    protected function typing()
    {
        $this->bot->sendChatAction(
            chat_id: $this->message->from->id,
            action: ChatActionType::Typing,
        );
    }

    /**
     * @throws \Telepath\Exceptions\TelegramException
     */
    public function respond(string $text): void
    {
        $this->bot->sendMessage(
            chat_id: $this->message->from->id,
            text: $text,
        );
    }

    protected function setReactionError()
    {
        $this->bot->setMessageReaction(
            chat_id: $this->message->from->id,
            message_id: $this->message->message_id,
            reaction: [ReactionTypeEmoji::make('⚡')]
        );
    }

    protected function setReactionBad()
    {
        $this->bot->setMessageReaction(
            chat_id: $this->message->from->id,
            message_id: $this->message->message_id,
            reaction: [ReactionTypeEmoji::make('👎')]
        );
    }

    protected function setReactionGood()
    {
        $this->bot->setMessageReaction(
            chat_id: $this->message->from->id,
            message_id: $this->message->message_id,
            reaction: [ReactionTypeEmoji::make('👍')]
        );
    }
}
