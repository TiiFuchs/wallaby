<?php

namespace App\Data\Uic918\FlexibleContent\DocumentDataTicket;

use Spatie\LaravelData\Data;

class TariffType extends Data
{
    public int $numberOfPassengers;

    public string $passengerType;

    public bool $restrictedToCountryOfResidence;

    public string $tariffDesc;
}
