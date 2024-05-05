<?php

namespace App\Console\Commands;

use App\Facades\ZXParser;
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

        $data = ZXParser::parse($filename);

        /** @var DTicket $dTicket */
        $dTicket = $pass->details;

        $dTicket->update([
            'valid_in' => now()->startOfMonth(),
            'barcode' => $data,
        ]);

        if (! $dTicket->wasChanged()) {
            $this->info("Barcode didn't change.");

            return;
        }

        $this->info("Barcode was saved to pass {$pass->serial_number} of {$dTicket->name}.");
    }
}
