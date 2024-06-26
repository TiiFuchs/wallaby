<?php

namespace App\Console\Commands\DTicket;

use App\Models\Pass;
use App\Models\PassDetails\DTicket;
use Illuminate\Console\Command;

class Parse extends Command
{
    protected $signature = 'd-ticket:parse {serialNumber : Pass to which the barcode should be saved} {file : Path to the screenshot}';

    protected $description = 'Parses a screenshot and saves it to a pass.';

    public function handle(): void
    {
        $serialNumber = $this->argument('serialNumber');
        $pass = Pass::wherePassTypeId('pass.one.tii.d-ticket')->whereSerialNumber($serialNumber)->firstOrFail();

        $filename = $this->argument('file');

        /** @var DTicket $ticket */
        $ticket = $pass->details;

        $success = $ticket->parseScreenshot($filename);

        if (! $success) {
            $this->error('No barcode found');

            return;
        }

        if (! $ticket->wasChanged()) {
            $this->info("Barcode didn't change.");

            return;
        }

        $this->info("Barcode was saved to pass {$pass->serial_number} for {$ticket->name}.");
    }
}
