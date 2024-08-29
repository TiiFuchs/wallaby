<?php

namespace App\Data\UIC918\FlexibleContent;

use App\Data\UIC918\FlexibleContent\DocumentDataTicket\DocumentDataTicket;
use Spatie\LaravelData\Data;

class DocumentData extends Data
{
    public DocumentDataTicket $ticket;
}
