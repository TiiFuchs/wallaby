<?php

namespace App\Data\UIC918\FlexibleContent\DocumentDataTicket;

use App\Data\UIC918\FlexibleContent\RegionalValidityType\RegionalValidityType;
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

    /** @var array<int, RegionalValidityType> */
    public array $validRegion;

    public int $validFromDay;

    public int $validFromTime;

    public int $validFromUTCOffset;

    public int $validUntilDay;

    public int $validUntilTime;
}
