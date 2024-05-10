<?php

namespace App\Console\Commands\CineStarCard;

use App\Jobs\UpdateCineStarCardData;
use App\Models\PassDetails\CineStarCard;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

use function Laravel\Prompts\progress;

class Update extends Command
{
    protected $signature = 'cinestarcard:update
                            {--id=* : Filter CineStarCard by ID}
                            {--email=* : Filter CineStarCard by email}
                            {--all : Update all CineStarCards}';

    protected $description = 'Updates CineStarCard data and pushes changed passes.';

    public function handle(): int
    {
        try {
            $cards = $this->relevantCards();
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if (count($cards) === 0) {
            $this->warn('There are no CineStarCards to update.');

            return self::SUCCESS;
        }

        progress('Queueing jobs...', $cards, function (CineStarCard $card) {
            UpdateCineStarCardData::dispatch($card);
        });
    }

    /**
     * @return Collection<CineStarCard>
     */
    protected function relevantCards(): Collection
    {
        if ($this->option('all')) {
            return CineStarCard::all();
        } elseif ($ids = $this->option('id')) {
            return CineStarCard::whereIn('id', $ids)->get();
        } elseif ($emails = $this->option('email')) {
            return CineStarCard::whereIn('email', $emails)->get();
        } else {
            throw new \InvalidArgumentException('Please specify one of the flags --all, --id= or --email=');
        }
    }
}
