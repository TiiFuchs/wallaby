<?php

namespace App\Console\Commands\Pass;

use App\Facades\QRTerminal;
use App\Models\Pass;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;

class DownloadPass extends Command
{
    protected $signature = 'pass:download
        {passTypeId? : Pass Type ID of the pass to download}
        {serialNumber? : Serial number of the pass to download}';

    protected $description = 'Command description';

    public function handle(): void
    {
        //
        $passTypeId = $this->argument('passTypeId');

        if (! $passTypeId) {
            $passTypeId = select(
                'Which pass type do you want to download?',
                Pass::select('pass_type_id')->distinct()->get()->pluck('pass_type_id'),
            );
        }

        $serialNumber = $this->argument('serialNumber');

        if (! $serialNumber) {
            $serialNumber = select(
                'Which serial number do you want to download?',
                Pass::wherePassTypeId($passTypeId)->select('serial_number')->distinct()->get()->pluck('serial_number'),
            );
        }

        $pass = Pass::wherePassTypeId($passTypeId)->whereSerialNumber($serialNumber)->first();

        $link = $pass->downloadLink();

        QRTerminal::generate($link);

        $this->info("Download your pass from $link");
    }
}
