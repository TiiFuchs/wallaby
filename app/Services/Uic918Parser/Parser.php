<?php

namespace App\Services\Uic918Parser;

use App\Data\Uic918\Record;

abstract class Parser
{
    public function __construct(
        protected string $version,
    )
    {
    }

    abstract public function parse(string $rawData): Record;
}
