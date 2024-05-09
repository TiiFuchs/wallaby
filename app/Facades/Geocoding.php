<?php

namespace App\Facades;

use App\Services\GoogleGeocodingService;
use Illuminate\Support\Facades\Facade;

class Geocoding extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GoogleGeocodingService::class;
    }
}
