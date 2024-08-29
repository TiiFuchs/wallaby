<?php

namespace App\Data\UIC918\FlexibleContent\RegionalValidityType;

use App\Exceptions\UIC918\InvalidDataException;
use Spatie\LaravelData\Data;

abstract class RegionalValidityType extends Data
{
    public static function fromArray(array $value): self
    {
        return match ($value[0]) {
            'zones' => ZoneType::from($value[1]),
            default => throw new InvalidDataException("Could not identify RegionalValidityType: {$value[0]}"),
        };
    }
}
