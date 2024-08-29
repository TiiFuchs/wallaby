<?php

namespace App\Services\UIC918\Parser;

use App\Data\UIC918\Record;

abstract class Parser
{
    public function __construct(
        protected string $version,
    ) {}

    abstract public function parse(string $rawData): Record;
}
