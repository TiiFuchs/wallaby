<?php

namespace App\Data\Uic918\TicketLayout;

use Spatie\LaravelData\Data;

class Formatting extends Data
{
    public function __construct(
        public readonly bool $bold,
        public readonly bool $italic,
        public readonly bool $small,
    ) {}

    public static function fromInt(int $formatting): self
    {
        return new self(
            in_array($formatting, [1, 3, 5, 7]),
            in_array($formatting, [2, 3, 6, 7]),
            in_array($formatting, [4, 5, 6, 7]),
        );
    }
}
