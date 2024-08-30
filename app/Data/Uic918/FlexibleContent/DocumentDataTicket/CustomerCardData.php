<?php

namespace App\Data\Uic918\FlexibleContent\DocumentDataTicket;

use App\Data\Uic918\FlexibleContent\TravelerType;
use Illuminate\Support\Collection;
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

    /** @var Collection<int, int> */
    public Collection $includedServices;

    public ExtensionData $extension;
}
