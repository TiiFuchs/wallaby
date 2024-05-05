<?php

namespace App\Telepath;

use App\Models\PassDetails\DTicket;
use Telepath\Bot;
use Telepath\Handlers\Message\Command;
use Telepath\Telegram\InlineKeyboardButton;
use Telepath\Telegram\InlineKeyboardMarkup;
use Telepath\Telegram\Update;

class GetPass
{
    #[Command('pass')]
    public function pass(Update $update, Bot $bot)
    {
        $ticket = DTicket::whereTelegramUserId($update->message->from->id)->first();

        if (! $ticket) {
            $bot->sendMessage(
                chat_id: $update->message->from->id,
                text: 'Du hast noch kein Deutschlandticket registriert. Bitte melde dich bei @TiiFuchs.'
            );

            return;
        }

        $bot->sendMessage(
            chat_id: $update->message->from->id,
            text: "Hier ist der Link für dein Deutschland-Ticket.\nDieser Link ist 60 Minuten gültig.",
            reply_markup: InlineKeyboardMarkup::make([[
                InlineKeyboardButton::make(
                    text: 'Zu Wallet hinzufügen',
                    url: $ticket->pass->downloadLink(),
                ),
            ]])
        );
    }
}
