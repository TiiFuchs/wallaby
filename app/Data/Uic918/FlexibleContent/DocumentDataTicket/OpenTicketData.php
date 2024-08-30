<?php

namespace App\Data\Uic918\FlexibleContent\DocumentDataTicket;

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

    public ?int $fromStationNum;

    public ?int $toStationNum;

    #[MapInputName('fromStationNameUTF8')]
    public ?string $fromStationName;

    #[MapInputName('toStationNameUTF8')]
    public ?string $toStationName;

    public ?string $validRegionDesc;

    public int $validFromDay;

    public int $validFromTime;

    public int $validFromUTCOffset;

    public int $validUntilDay;

    public int $validUntilTime;

    public string $classCode;

    /** @var array<int, TariffType> */
    public array $tariffs;

    public ?int $price;

    /** @var array<int, IncludedOpenTicketType> */
    public ?array $includedAddOns;
}
