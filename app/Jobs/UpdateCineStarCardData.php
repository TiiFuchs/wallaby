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

    public function __construct(protected CineStarCard $card) {}

    public function handle(): void
    {
        $this->card->fetchData();

        if ($this->card->wasChanged()) {
            $this->card->pass->pushToDevices();
        }
    }
}
