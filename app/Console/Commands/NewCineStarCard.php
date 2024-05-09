<?php

namespace App\Console\Commands;

use App\Exceptions\CineStarCard\InvalidAuthenticationException;
use App\Facades\QRTerminal;
use App\Models\PassDetails\CineStarCard;
use Illuminate\Console\Command;

use function Laravel\Prompts\password;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class NewCineStarCard extends Command
{
    protected $signature = 'cinestarcard:new';

    protected $description = 'Creates a new CineStarCard';

    public function handle(): void
    {
        $username = text('Username', required: true);
        $password = password('Password', required: true);

        $cinestarCard = CineStarCard::create([
            'username' => $username,
            'password' => $password,
        ]);

        try {
            spin(fn () => $cinestarCard->fetchData(), 'Fetching data...');
        } catch (InvalidAuthenticationException $e) {
            $cinestarCard->delete();
            $this->error($e->getMessage());

            return;
        }

        $link = $cinestarCard->pass->downloadLink();

        QRTerminal::generate($link);

        $this->info("Download your pass from $link");

    }
}
