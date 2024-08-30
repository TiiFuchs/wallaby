<?php

namespace App\Data\Uic918\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class JsonCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        try {
            return json_decode($value, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return Uncastable::create();
        }
    }
}
