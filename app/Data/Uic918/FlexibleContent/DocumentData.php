<?php

namespace App\Data\Uic918\FlexibleContent;

use App\Data\Uic918\FlexibleContent\DocumentDataTicket\DocumentDataTicket;
use Spatie\LaravelData\Data;

class DocumentData extends Data
{
    public DocumentDataTicket $ticket;
}
