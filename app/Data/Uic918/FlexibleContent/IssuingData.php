<?php

namespace App\Data\Uic918\FlexibleContent;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

class IssuingData extends Data
{
    public int $securityProviderNum;

    public int $issuerNum;

    public int $issuingYear;

    public int $issuingDay;

    public int $issuingTime;

    public string $issuerName;

    public bool $specimen;

    public bool $securePaperTicket;

    public bool $activated;

    public string $currency;

    public int $currencyFract;

    public string $issuerPNR;

    public function issuingDate(): CarbonImmutable
    {
        return CarbonImmutable::createFromDate($this->issuingYear, 1, 1)
            ->setTimezone('UTC')
            ->setTime(0, 0)
            ->addDays($this->issuingDay - 1)
            ->addMinutes($this->issuingTime);
    }
}
