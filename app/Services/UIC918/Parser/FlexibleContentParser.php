<?php

namespace App\Services\UIC918\Parser;

use App\Data\UIC918\FlexibleContent\FlexibleContent;
use App\Exceptions\UIC918\InvalidDataException;
use Illuminate\Support\Facades\Process;

class FlexibleContentParser extends Parser
{
    public function __construct(
        protected string $version,
    ) {
        //
        ray($this->version);
    }

    public function parse(string $rawData): FlexibleContent
    {
        $process = Process::run([
            base_path('uic-asn1-parser/.venv/bin/python'),
            base_path('uic-asn1-parser/decode.py'),
            $this->version,
            base64_encode($rawData),
        ]);

        if ($process->failed()) {
            throw new InvalidDataException('Couldn\'t parse flexible content');
        }

        $data = json_decode($process->output());

        return FlexibleContent::from($data);
    }
}
