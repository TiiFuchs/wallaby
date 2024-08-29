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
        $result = Process::env([
            'LC_ALL' => 'de_DE.UTF-8',
        ])->run([
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
        $raw = mb_substr($output, mb_strpos($output, "Raw result:\n") + 12);
        $raw = mb_substr($raw, 0, mb_strpos($raw, "\nParsed result:"));

        // Convert back to iso-8859-1
        return mb_convert_encoding($raw, 'latin1', 'utf-8');
    }
}
