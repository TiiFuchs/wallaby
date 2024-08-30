<?php

namespace App\Data\Uic918\FlexibleContent;

use App\Data\Uic918\Record;
use Illuminate\Support\Collection;

class FlexibleContent extends Record
{
    public string $version;

    public IssuingData $issuingDetail;

    public TravelerData $travelerDetail;

    /** @var Collection<int, DocumentData> */
    public Collection $transportDocument;
}
