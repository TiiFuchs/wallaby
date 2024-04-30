<?php

namespace App\Models\PassDetails;

use App\Models\Pass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class PassDetails extends Model
{
    abstract public function getJsonData(): array;

    public function pass(): MorphOne
    {
        return $this->morphOne(Pass::class, 'details');
    }
}
