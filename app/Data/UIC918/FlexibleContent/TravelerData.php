<?php

namespace App\Data\UIC918\FlexibleContent;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class TravelerData extends Data
{
    /** @var array<int, TravelerType> */
    public array $traveler;

    public string|Optional $preferredLanguage;

    public string|Optional $groupName;
}
