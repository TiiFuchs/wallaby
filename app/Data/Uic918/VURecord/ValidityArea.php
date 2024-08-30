<?php

namespace App\Data\Uic918\VURecord;

use Spatie\LaravelData\Data;

class ValidityArea extends Data
{
    public function __construct(
        public int $type,
        public string $kvpOrgId,
        public string $tp1,
    ) {}
}
