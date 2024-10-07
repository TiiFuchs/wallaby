<?php

namespace App\Telepath;

use Telepath\Handlers\Message\Command;
use Telepath\Telegram\Update;

class Start
{
    #[Command('start')]
    public function start(Update $update)
    {
        $update->message->replyToUser(
            "Hi,\nwenn du bereits ein Deutschland Ticket besitzt, kann ich dir daraus einen Wallet-Pass für dein iPhone erstellen.\n🚫 Ich kann dir <strong>KEIN</strong> D-Ticket verkaufen.\n\n💬 Um anzufangen, wende dich an @TiiFuchs.",
            parse_mode: 'HTML',
        );
    }
}
