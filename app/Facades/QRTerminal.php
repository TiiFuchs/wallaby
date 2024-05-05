<?php

namespace App\Facades;

use App\Services\QRTerminalService;
use Illuminate\Support\Facades\Facade;

class QRTerminal extends Facade
{
    protected static function getFacadeAccessor()
    {
        return QRTerminalService::class;
    }
}
