<?php

namespace App\Data\Uic918\FlexibleContent\RegionalValidityType;

use Spatie\LaravelData\Attributes\MapInputName;

class ZoneType extends RegionalValidityType
{
    public int $carrierNum;

    #[MapInputName('carrierIA5')]
    public string $carrier;

    public string $stationCodeTable;

    /** @var array<int, int> */
    public array $zoneId;
}
