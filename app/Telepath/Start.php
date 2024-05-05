<?php

namespace App\Telepath;

use Telepath\Handlers\Message\Command;
use Telepath\Telegram\Update;

class Start
{
    #[Command('start')]
    public function start(Update $update)
    {
        $update->message->replyToUser("Hi, ich kann dir aus einem Screenshot aus dem DB Navigator einen Wallet Pass deines Deutschland Tickets erstellen.\nUm anzufangen, melde dich bei @TiiFuchs.");
    }
}
