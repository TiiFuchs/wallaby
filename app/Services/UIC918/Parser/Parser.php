<?php

namespace App\Services\UIC918\Parser;

use App\Data\UIC918\Record;

abstract class Parser
{
    abstract public function parse(string $rawData): Record;
}
