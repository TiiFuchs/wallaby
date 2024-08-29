<?php

namespace App\Data\UIC918\FlexibleContent;

use Carbon\Carbon;
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

    public function issuingDate(): Carbon
    {
        $date = Carbon::createFromDate($this->issuingYear, 1, 1)
            ->setTime(0, 0);

        $date->addDays($this->issuingDay - 1);
        $date->addMinutes($this->issuingTime);

        return $date;
    }
}
