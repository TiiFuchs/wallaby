<?php

namespace App\Data\Geocoding;

use Spatie\LaravelData\Data;

class Address extends Data
{
    public function __construct(
        public string $address,
        public Coordinates $coordinates,
    ) {}
}
