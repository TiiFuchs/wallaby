<?php

namespace App\Console\Commands\Certificates;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;

class Convert extends Command
{
    protected $signature = 'cert:convert {path : Path to certificate file (.p12)}
                            {--p|password= : Password for private key}
                            {--new-password= : New password, if password should be changed}';

    protected $description = 'Converts a Certificate for usage with this app';

    public function handle(): int
    {
        $certFile = $this->argument('path');

        if (! is_readable($certFile)) {
            $this->error('Certificate file not found or not readable');

            return static::FAILURE;
        }

        $password = $this->option('password');
        if (! $password) {
            $password = password('Password for certificate');
        }

        $newPassword = $this->option('new-password');
        if (! $newPassword && confirm('Would you like to change the password?')) {
            $newPassword = password('Specify the new password');
        } elseif (! $newPassword) {
            $newPassword = $password;
        }

        $pemFile = tempnam(sys_get_temp_dir(), 'pass_');
        $keyFile = tempnam(sys_get_temp_dir(), 'key_');
        $outFile = dirname($certFile).DIRECTORY_SEPARATOR.basename($certFile, '.p12').'_aes256.p12';

        // Step 1: Extract certificate and key
        $result = Process::run([
            'openssl', 'pkcs12',
            '-in', $certFile,
            '-out', $pemFile,
            '-passin', 'pass:'.$password,
            '-passout', 'pass:'.$newPassword,
            '-legacy',
        ]);

        if ($result->failed()) {
            $this->error('Error on certificate extraction: '.$result->errorOutput());

            return static::FAILURE;
        }

        // Step 2: Convert key to more secure format
        $result2 = Process::run([
            'openssl', 'rsa',
            '-in', $pemFile,
            '-out', $keyFile,
            '-passin', 'pass:'.$newPassword,
            '-passout', 'pass:'.$newPassword,
            '-aes256',
        ]);

        if ($result2->failed()) {
            $this->error('Error on private key conversion: '.$result2->errorOutput());

            return static::FAILURE;
        }

        // Step 3: Repackage into new p12 file
        $result3 = Process::run([
            'openssl', 'pkcs12',
            '-export',
            '-in', $pemFile,
            '-inkey', $keyFile,
            '-out', $outFile,
            '-passin', 'pass:'.$newPassword,
            '-passout', 'pass:'.$newPassword,
        ]);

        if ($result3->failed()) {
            $this->error('Error on certificate creation: '.$result3->errorOutput());

            return static::FAILURE;
        }

        // Cleanup
        unlink($pemFile);
        unlink($keyFile);

        $this->info('Successfully converted certificate file');

        return static::SUCCESS;
    }
}
