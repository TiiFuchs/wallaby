<?php

namespace App\Services\UIC918\Parser;

use App\Data\UIC918\MainRecord;
use App\Utils\ByteReader;

class MainRecordParser extends Parser
{
    public function parse(string $rawData): MainRecord
    {
        $data = new ByteReader($rawData);

        return new MainRecord(...[
            'companyCode' => $data->next(4),
            'ticketKey' => $data->unpack('A20'),
            'editionTime' => $data->next(12),
            'flags' => $data->next(1),
            'editionLanguage' => $data->next(2),
            'secondLanguage' => $data->next(2),
        ]);
    }
}
