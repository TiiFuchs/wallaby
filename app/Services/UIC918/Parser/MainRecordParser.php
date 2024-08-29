<?php

namespace App\Services\UIC918\Parser;

use App\Data\UIC918\MainRecord;
use App\Utils\ByteReader;

class MainRecordParser extends Parser
{
    public function __construct(
        protected string $version,
    ) {
        //
    }

    public function parse(string $rawData): MainRecord
    {
        $mainRecord = new MainRecord;

        $data = new ByteReader($rawData);

        return $mainRecord;
    }
}
