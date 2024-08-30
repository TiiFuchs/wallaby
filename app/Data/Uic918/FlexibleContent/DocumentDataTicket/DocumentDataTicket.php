<?php

namespace App\Data\Uic918\FlexibleContent\DocumentDataTicket;

use App\Exceptions\Uic918\InvalidDataException;
use Spatie\LaravelData\Data;

abstract class DocumentDataTicket extends Data
{
    public static function fromArray(array $value): self
    {
        return match ($value[0]) {
            'openTicket' => OpenTicketData::from($value[1]),
            'customerCard' => CustomerCardData::from($value[1]),
            default => throw new InvalidDataException("Could not identify DocumentDataTicket type: {$value[0]}"),
        };
    }
}
