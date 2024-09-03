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
                text: '🚫 Du bist nicht für diesen Service freigeschaltet. Bitte melde dich bei @TiiFuchs.'
            );

            return;
        }

        $bot->sendMessage(
            chat_id: $update->message->from->id,
            text: "Hier ist dein Deutschlandticket Wallet Pass.\nBitte öffne den Link in Safari.",
            reply_markup: InlineKeyboardMarkup::make([[
                InlineKeyboardButton::make(
                    text: 'Zu Wallet hinzufügen',
                    url: $ticket->pass->downloadLink(),
                ),
            ]])
        );
    }
}
