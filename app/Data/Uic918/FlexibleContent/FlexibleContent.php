<?php

namespace App\Data\Uic918\FlexibleContent;

use App\Data\Uic918\Record;

class FlexibleContent extends Record
{
    public IssuingData $issuingDetail;

    public TravelerData $travelerDetail;

    /** @var array<int, DocumentData> */
    public array $transportDocument;
}
