<?php

namespace App\Data\UIC918\TicketLayout;

use Spatie\LaravelData\Data;

class Field extends Data
{
    public function __construct(
        public int $line,
        public int $column,
        public int $height,
        public int $width,
        public Formatting $formatting,
        public int $length,
        public string $text,
    ) {}
}
