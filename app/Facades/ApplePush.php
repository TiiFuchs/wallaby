<?php

namespace App\Facades;

use App\Services\ApplePushService;
use Illuminate\Support\Facades\Facade;

class ApplePush extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ApplePushService::class;
    }
}
