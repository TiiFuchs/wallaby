<?php

namespace App\Console\Commands;

use App\Models\Pass;
use App\Models\PassDetails\DTicket;
use Illuminate\Console\Command;

class ParseDTicket extends Command
{
    protected $signature = 'dticket:parse {serialNumber : Pass to which the barcode should be saved} {file : Path to the screenshot}';

    protected $description = 'Parses a screenshot and saves it to a pass.';

    public function handle(): void
    {
        $serialNumber = $this->argument('serialNumber');
        $pass = Pass::wherePassTypeId('pass.one.tii.d-ticket')->whereSerialNumber($serialNumber)->firstOrFail();

        $filename = $this->argument('file');

        /** @var DTicket $ticket */
        $ticket = $pass->details;

        $ticket->parseScreenshot($filename);

        if (! $ticket->wasChanged()) {
            $this->info("Barcode didn't change.");

            return;
        }

        $this->info("Barcode was saved to pass {$pass->serial_number} for {$ticket->name}.");
    }
}
