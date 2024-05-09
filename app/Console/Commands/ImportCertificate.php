<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\password;

class ImportCertificate extends Command
{
    protected $signature = 'cert:import {path : Path to certificate file (.p12)}
                            {--p|password= : Password for certificate file}';

    protected $description = 'Imports certificate';

    public function handle(): int
    {
        $path = $this->argument('path');

        $password = $this->option('password');
        if (! $password) {
            $password = password('Password for certificate file');
        }

        $p12File = file_get_contents($path);
        $p12Readable = openssl_pkcs12_read($p12File, $certs, $password);

        if (! $p12Readable) {
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

        $passTypeId = $info['subject']['UID'];

        if (! str_starts_with($passTypeId, 'pass.')) {
            $this->error('Certificates UID doesn\'t seem to represent a Pass Type ID. This might be a coding error.');

            return static::FAILURE;
        }

        $relTargetPath = "app/certificates/{$passTypeId}.p12";
        $targetFile = storage_path($relTargetPath);

        $newPassword = config('passkit.certificate_password');
        if (! $newPassword) {
            $this->error('No certificate password configured in your .env file. Please set CERT_PASSWORD to a secure password.');

            return static::FAILURE;
        }

        if ($password !== $newPassword) {
            // Password will get changed
            $this->warn('The password of the imported certificate will be changed to the CERT_PASSWORD password in your .env file.');
        }

        $exported = openssl_pkcs12_export_to_file($certs['cert'], $targetFile, $certs['pkey'], $newPassword);

        if (! $exported) {
            $this->error('Failed to save p12 file: '.openssl_error_string());

            return static::FAILURE;
        }

        $this->info("Certificate was imported in {$relTargetPath}");

        return static::SUCCESS;
    }
}
