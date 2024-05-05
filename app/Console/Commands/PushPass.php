<?php

namespace App\Console\Commands;

use App\Models\Pass;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class PushPass extends Command
{
    protected $signature = 'pass:push {passTypeId? : Pass Type ID of the passes to push}
                                      {serialNumber? : Serial number of the pass to push}';

    protected $description = 'Pushes updated passes to its devices.';

    public function handle(): void
    {
        $passTypeId = $this->argument('passTypeId');

        if (! $passTypeId) {
            $passTypeId = select(
                'Which pass types should be pushed?',
                Pass::select('pass_type_id')->distinct()->get()->pluck('pass_type_id'),
            );
        }

        $serialNumber = $this->argument('serialNumber');

        if (! $serialNumber && ! confirm('You didn\'t specify any serialNumber. Do you wish to push all passes?')) {
            $serialNumber = text('Which serial number do you wish to push?');
        }

        $passes = Pass::wherePassTypeId($passTypeId)
            ->when($serialNumber, fn ($query) => $query->whereSerialNumber($serialNumber))
            ->get();

        if ($passes->count() === 0) {
            $this->warn('There are no passes matching the criteria.');

            return;
        }

        $this->withProgressBar($passes, function (Pass $pass) {

            $pass->pushToDevices();

        });
    }
}
