<?php

namespace App\Facades;

use App\Services\Uic918Parser\TicketParser;
use Illuminate\Support\Facades\Facade;

class UicTicketParser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TicketParser::class;
    }
}
