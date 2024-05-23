<?php

namespace App\Services;

use App\Exceptions\ZXParserException;
use Illuminate\Support\Facades\Process;

class ZXParserService
{
    /**
     * @return string
     *
     * @throws ZXParserException
     */
    public function parse(string $imagePath)
    {
        // Get paths
        $javaPath = config('paths.java_bin');
        $javaSEPath = base_path('bin/zebra-crossing/javase-3.5.3.jar');
        $corePath = base_path('bin/zebra-crossing/core-3.5.3.jar');
        $jCommanderPath = base_path('bin/zebra-crossing/jcommander-1.83.jar');

        // Run java command
        $result = Process::run([
            $javaPath,
            '-cp', "$javaSEPath:$corePath:$jCommanderPath",
            'com.google.zxing.client.j2se.CommandLineRunner',
            $imagePath,
            '--possible_formats', 'AZTEC',
        ]);
        $output = $result->output();

        if (str_contains($output, 'No barcode found')) {
            throw new ZXParserException('No barcode found', 1);
        }

        // Parse raw result
        $raw = substr($output, strpos($output, "Raw result:\n") + 12);
        $raw = substr($raw, 0, strpos($raw, "\nParsed result:"));

        return $raw;
    }
}
