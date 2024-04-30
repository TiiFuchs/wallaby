<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_library_identifier',
        'push_token',
    ];

    public function passes(): BelongsToMany
    {
        return $this->belongsToMany(Pass::class, 'registrations')
            ->withTimestamps();
    }
}
