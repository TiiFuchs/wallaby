<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PHPZxing\PHPZxingDecoder;
use PHPZxing\ZxingImage;

class DTicketParse extends Command
{
    protected $signature = 'dticket:parse';

    protected $description = 'Command description';

    public function handle(): void
    {
        $original = '';
        $decoder = new PHPZxingDecoder();
        /** @var ZxingImage $data */
        $data = $decoder->decode(storage_path('app/screenshot.jpeg'));

    }
}
