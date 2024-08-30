<?php

namespace App\Data\Uic918\FlexibleContent\DocumentDataTicket;

use App\Data\Uic918\FlexibleContent\RegionalValidityType\RegionalValidityType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class IncludedOpenTicketType extends Data
{
    #[MapInputName('productOwnerIA5')]
    public string $productOwner;

    public int $productIdNum;

    public int $externalIssuerId;

    public int $issuerAutorizationId;

    public string $stationCodeTable;

    /** @var Collection<int, RegionalValidityType> */
    public Collection $validRegion;

    public int $validFromDay;

    public int $validFromTime;

    public int $validFromUTCOffset;

    public int $validUntilDay;

    public int $validUntilTime;

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
