<?php

namespace App\Data\UIC918\FlexibleContent\DocumentDataTicket;

use App\Data\UIC918\FlexibleContent\TravelerType;
use Spatie\LaravelData\Attributes\MapInputName;

class CustomerCardData extends DocumentDataTicket
{
    public TravelerType $customer;

    #[MapInputName('cardIdIA5')]
    public string $cardId;

    public int $validFromYear;

    public int $validFromDay;

    public int $validUntilYear;

    public int $validUntilDay;

    public string $classCode;

    public string $cardTypeDescr;

    /** @var array<int, int> */
    public array $includedServices;

    public ExtensionData $extension;
}
