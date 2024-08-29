<?php

namespace App\Data\CineStarCard;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class ProfileData extends Data
{
    public function __construct(
        public string $customerNumber,
        public string $gender,
        public string $firstName,
        public string $lastName,
        public string $streetAndNumber,
        public string $postalCode,
        public string $city,
        public string $regularCinema,
        public string $dateOfBirth,
        public float $premiumPoints,
    ) {}
}
