<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Base64Cast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return base64_decode($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return base64_encode($value);
    }
}
