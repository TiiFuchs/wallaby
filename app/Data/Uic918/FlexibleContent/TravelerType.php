<?php

namespace App\Data\Uic918\FlexibleContent;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class TravelerType extends Data
{
    public string $firstName;

    public string $lastName;

    public ?int $yearOfBirth;

    /** @var int|null Only in version 1.3 */
    public ?int $dayOfBirth;

    /** @var int|null Only in version 3.0 */
    public ?int $monthOfBirth;

    /** @var int|null Only in version 3.0 */
    public ?int $dayOfBirthInMonth;

    public bool $ticketHolder;

    public function birthDate(): ?Carbon
    {
        if (! is_null($this->yearOfBirth) && ! is_null($this->monthOfBirth) && ! is_null($this->dayOfBirthInMonth)) {

            // Version 3.0
            return Carbon::createFromDate($this->yearOfBirth, $this->monthOfBirth, $this->dayOfBirthInMonth)
                ->setTime(0, 0);

        } elseif (! is_null($this->yearOfBirth) && ! is_null($this->dayOfBirth)) {

            // Version 1.3
            return Carbon::createFromDate($this->yearOfBirth, 1, 1)
                ->setTime(0, 0)
                ->addDays($this->dayOfBirth - 1);

        } else {

            return null;

        }
    }
}
