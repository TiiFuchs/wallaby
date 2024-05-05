<?php

namespace App\Console\Commands;

use App\Facades\QRTerminal;
use App\Models\PassDetails\DTicket;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

class NewDTicket extends Command
{
    protected $signature = 'dticket:new';

    protected $description = 'Generates a new D-Ticket.';

    public function handle(): void
    {
        $name = text(
            label: 'Name',
            required: true
        );

        $telegramUserId = text(
            label: 'Telegram User ID',
            required: false,
            validate: [
                'telegram_user_id' => 'integer',
            ],
        ) ?: null;

        $ticket = DTicket::create([
            'name' => $name,
            'telegram_user_id' => $telegramUserId,
        ]);

        $link = $ticket->pass->downloadLink();

        $this->info("Download your pass from $link");

        QRTerminal::generate($link);

    }
}
