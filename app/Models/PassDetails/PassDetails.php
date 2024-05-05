<?php

namespace App\Models\PassDetails;

use App\Models\Pass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class PassDetails extends Model
{
    abstract public function getJsonData(): array;

    abstract public function getPassTypeId(): string;

    protected static function booted()
    {
        static::created(function (PassDetails $details) {
            $details->pass()->create([
                'pass_type_id' => $details->getPassTypeId(),
            ]);
        });
    }

    public function pass(): MorphOne
    {
        return $this->morphOne(Pass::class, 'details');
    }
}
