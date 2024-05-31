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
        $comparisonDate = now()->startOfMonth();

        if (now()->isLastOfMonth()) {
            $comparisonDate = $comparisonDate->addMonth();
        }

        $dateString = $comparisonDate->translatedFormat('F Y');

        $textPool = [
            "Denk dran, mir dein neues Deutschlandticket für <b>{$dateString}</b> zuzusenden, damit ich deinen Wallet Pass aktualisieren kann.",
            "Vergiss nicht, mir dein neues Deutschlandticket für <b>{$dateString}</b> zuzusenden, damit ich deinen Wallet Pass aktualisieren kann.",
            "Bitte sende mir dein neues Deutschlandticket für <b>{$dateString}</b>, damit ich deinen Wallet Pass aktualisieren kann.",
            "Ich benötige dein neues Deutschlandticket für <b>{$dateString}</b>, um deinen Wallet Pass zu aktualisieren.",
            "Dein neues Deutschlandticket für <b>{$dateString}</b> fehlt mir noch, um deinen Wallet Pass zu aktualisieren.",
            "Ich warte noch auf dein neues Deutschlandticket für <b>{$dateString}</b>, um deinen Wallet Pass zu aktualisieren.",
        ];
        $text = $textPool[array_rand($textPool)];

        // Go over every DTicket
        foreach (DTicket::whereNotNull('valid_in')->get() as $ticket) {

            if ($ticket->valid_in->equalTo($comparisonDate)) {
                continue;
            }

            Telepath::bot()->sendMessage(
                $ticket->telegram_user_id,
                $text,
                parse_mode: 'HTML',
            );

        }

    }
}
