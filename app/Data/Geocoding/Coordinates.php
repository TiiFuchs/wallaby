<?php

namespace App\Data\Geocoding;

use Spatie\LaravelData\Data;

class Coordinates extends Data
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {}
}
