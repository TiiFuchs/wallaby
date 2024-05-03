<?php

namespace App\Console\Commands;

use App\Models\Pass;
use App\Models\PassDetails\DTicket;
use Illuminate\Console\Command;
use PHPZxing\PHPZxingDecoder;
use PHPZxing\ZxingImage;

class DTicketParse extends Command
{
    protected $signature = 'dticket:parse {serialNumber : Pass to which the barcode should be saved} {file : Path to the screenshot}';

    protected $description = 'Parses a screenshot and saves it to a pass.';

    public function handle(): void
    {
        $serialNumber = $this->argument('serialNumber');
        $pass = Pass::wherePassTypeId('pass.one.tii.d-ticket')->whereSerialNumber($serialNumber)->firstOrFail();

        $filename = $this->argument('file');

        $decoder = new PHPZxingDecoder([
            'possible_formats' => 'AZTEC',
        ]);
        /** @var ZxingImage $data */
        $data = $decoder->decode($filename);

        /** @var DTicket $dTicket */
        $dTicket = $pass->details;

        file_put_contents(base_path('testfile.txt'), $data->getImageValue());

        $dTicket->update([
            'barcode' => $data->getImageValue(),
        ]);

        $this->info("Barcode was saved to pass {$pass->serial_number} of {$dTicket->name}.");
    }
}
