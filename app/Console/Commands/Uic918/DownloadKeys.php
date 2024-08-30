<?php

namespace App\Console\Commands\Uic918;

use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class DownloadKeys extends Command
{
    protected $signature = 'uic:download-keys';

    protected $description = 'Downloads public keys for ticket verification';

    const CERTIFICATE_URI = 'https://assets.static-bahn.de/dam/jcr:32c43130-6e38-4b5e-92db-d46068492c2f/DB-Zertifikate_Produktiv.zip';

    public function handle(): void
    {
        /** @var Response $response */
        $zipContents = spin(
            callback: fn() => file_get_contents(self::CERTIFICATE_URI),
            message: 'Downloading...'
        );

        if ($zipContents === false) {
            $this->error('Could not download public keys. Maybe the URL is wrong...');
            $this->comment('See https://bahn.de/barcode for more information.');

            return;
        }

        $tempfile = tempnam(sys_get_temp_dir(), 'wallaby_keys_');
        file_put_contents($tempfile, $zipContents);
        unset($zipContents); // Free space

        $zip = new \ZipArchive;
        $zip->open($tempfile);

        $bar = progress('Extracting...', steps: $zip->numFiles);
        $bar->start();

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $basename = basename($filename);

            copy("zip://{$tempfile}#{$filename}", storage_path("app/uic_public_keys/{$basename}"));
            $bar->advance();
        }

        $bar->finish();

        $zip->close();
        unlink($tempfile);

        $this->info('Download complete.');
    }
}
