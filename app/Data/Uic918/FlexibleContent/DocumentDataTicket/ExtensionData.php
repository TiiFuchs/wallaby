<?php

namespace App\Data\Uic918\FlexibleContent\DocumentDataTicket;

use App\Data\Uic918\Casts\JsonCast;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class ExtensionData extends Data
{
    public string $extensionId;

    #[WithCast(JsonCast::class)]
    public \stdClass $extensionData;
}
