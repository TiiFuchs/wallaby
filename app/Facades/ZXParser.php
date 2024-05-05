<?php

namespace App\Facades;

use App\Services\ZXParserService;
use Illuminate\Support\Facades\Facade;

class ZXParser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ZXParserService::class;
    }
}
