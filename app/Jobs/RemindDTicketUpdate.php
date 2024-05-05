<?php

namespace App\Jobs;

use App\Models\PassDetails\DTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Telepath\Laravel\Facades\Telepath;

class RemindDTicketUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        if (! now()->isLastOfMonth()) {
            return;
        }

        $comparisonDate = now()->startOfMonth()->addMonth();
        $dateString = $comparisonDate->translatedFormat('F Y');

        // Go over every DTicket
        foreach (DTicket::whereNotNull('valid_in')->get() as $ticket) {

            if ($ticket->valid_in->equalTo($comparisonDate)) {
                continue;
            }

            Telepath::bot()->sendMessage(
                $ticket->telegram_user_id,
                "Heute ist der letzte Tag im Monat.\nDenk dran, mir dein neues Deutschlandticket für <b>{$dateString}</b> zuzusenden, damit ich deinen Wallet Pass aktualisieren kann.",
                parse_mode: 'HTML',
            );

        }

    }
}
