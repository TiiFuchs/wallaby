<?php

namespace App\Data\Uic918\VURecord;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class Efs extends Data
{
    public int $berechtigungsNr;

    public string $kvpOrgId;

    public int $produktNr;

    public string $pvOrgId;

    public CarbonImmutable $gueltigAb;

    public CarbonImmutable $gueltigBis;

    public float $preis;

    public int $sequenzNr;

    /** @var array<int, ValidityArea> */
    public array $validAreas = [];
}
