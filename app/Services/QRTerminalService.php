<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;

class QRTerminalService
{
    public function generate(string $text): void
    {
        $systemName = php_uname('s');
        $machineType = php_uname('m');

        $process = Process::run([
            "bin/qrterminal/qrterminal_{$systemName}_{$machineType}",
            escapeshellarg($text),
        ], function (string $type, string $output) {
            echo $output;
        });
    }
}
