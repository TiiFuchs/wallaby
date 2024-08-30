<?php

namespace App\Data\Uic918\FlexibleContent\DocumentDataTicket;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
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

    /** @var Collection<int, TariffType> */
    public Collection $tariffs;

    public ?int $price;

    /** @var Collection<int, IncludedOpenTicketType> */
    public ?Collection $includedAddOns;

    public function validFrom(CarbonImmutable $issuingDate): CarbonImmutable
    {
        return $issuingDate->utcOffset($this->validFromUTCOffset * -15)
            ->setTime(0, 0)
            ->addDays($this->validFromDay)
            ->addMinutes($this->validFromTime ?? 0);
    }

    public function validUntil(CarbonImmutable $issuingDate): CarbonImmutable
    {
        return $issuingDate->utcOffset($this->validFromUTCOffset * -15)
            ->setTime(0, 0)
            ->addDays($this->validUntilDay)
            ->addMinutes($this->validUntilTime ?? 0);
    }
}
