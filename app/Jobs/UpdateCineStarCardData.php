<?php

namespace App\Jobs;

use App\Models\PassDetails\CineStarCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCineStarCardData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle(): void
    {
        foreach (CineStarCard::all() as $card) {

            $card->updateData();

            if ($card->wasChanged()) {
                $card->pass->pushToDevices();
            }

        }
    }
}
