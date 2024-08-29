<?php

namespace App\Data\UIC918\FlexibleContent\DocumentDataTicket;

use Spatie\LaravelData\Attributes\MapInputName;

class OpenTicketData extends DocumentDataTicket
{
    #[MapInputName('referenceIA5')]
    public string $reference;

    public int $productIdNum;

    #[MapInputName('productIdIA5')]
    public string $productId;

    public bool $returnIncluded;

    public string $stationCodeTable;

    public int $validFromDay;

    public int $validFromTime;

    public int $validFromUTCOffset;

    public int $validUntilDay;

    public int $validUntilTime;

    public string $classCode;

    /** @var array<int, TariffType> */
    public array $tariffs;

    public int $price;

    /** @var array<int, IncludedOpenTicketType> */
    public array $includedAddOns;
}
