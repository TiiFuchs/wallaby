<?php

namespace App\Facades;

use App\Services\CineStarCardService;
use Illuminate\Support\Facades\Facade;

class CineStarCard extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CineStarCardService::class;
    }
}
