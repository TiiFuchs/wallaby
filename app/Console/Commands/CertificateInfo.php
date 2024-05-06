<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;

use function Laravel\Prompts\password;

class CertificateInfo extends Command
{
    protected $signature = 'cert:info {path : Path to certificate file (.p12)}
                            {--p|password= : Password for certificate file}';

    protected $description = 'Displays information about the certificate';

    public function handle(): int
    {
        $certPath = $this->argument('path');

        $password = $this->option('password');
        if (! $password) {
            $password = password('Password for certificate file', required: true);
        }

        $p12Cert = file_get_contents($certPath);

        $success = openssl_pkcs12_read($p12Cert, $certs, $password);

        if (! $success) {
            $error = openssl_error_string();

            if (str_contains($error, 'digital envelope routines::unsupported')) {
                $this->error('Certificate uses insecure algorithms. Please use `php artisan cert:convert` to convert it.');
            } else {
                $this->error('OpenSSL error: '.$error);
            }

            return static::FAILURE;
        }

        $info = openssl_x509_parse($certs['cert']);

        if (! str_contains($info['subject']['CN'], 'Pass Type ID:')) {
            $this->error('This certificate is no Pass Type ID Certificate');

            return static::FAILURE;
        }

        // Parse table
        $table = new Table($this->output);

        $table->setVertical();
        $table->setStyle('box');

        $table->setHeaders([
            'Pass Type ID',
            'Team Identifier',
            'Valid from',
            'Valid until',
        ]);

        $table->setRow(0, [
            $info['subject']['UID'],
            $info['subject']['OU'],
            Carbon::createFromTimestamp($info['validFrom_time_t'])->translatedFormat('d.m.Y H:i'),
            Carbon::createFromTimestamp($info['validTo_time_t'])->translatedFormat('d.m.Y H:i'),
        ]);

        $table->render();

        return static::SUCCESS;
    }
}
