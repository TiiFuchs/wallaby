<?php

namespace App\Data\UIC918\FlexibleContent;

use App\Data\UIC918\Record;

class FlexibleContent extends Record
{
    public IssuingData $issuingDetail;

    public TravelerData $travelerDetail;

    /** @var array<int, DocumentData> */
    public array $transportDocument;
}
