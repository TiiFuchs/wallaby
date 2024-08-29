<?php

namespace App\Data\UIC918\VURecord;

use Spatie\LaravelData\Data;

class ValidityArea extends Data
{
    public function __construct(
        public int $type,
        public string $kvpOrgId,
        public string $tp1,
    ) {}
}
