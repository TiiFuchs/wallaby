<?php

namespace App\Data\UIC918\FlexibleContent;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class TravelerType extends Data
{
    public string $firstName;

    public string $lastName;

    public int $yearOfBirth;

    public int $monthOfBirth;

    public int $dayOfBirthInMonth;

    public bool $ticketHolder;

    public function birthday(): Carbon
    {
        return Carbon::createFromDate($this->yearOfBirth, $this->monthOfBirth, $this->dayOfBirthInMonth)
            ->setTime(0, 0);
    }
}
