<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'device_library_identifier' => Str::random(32),
            'push_token' => Str::random(64),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
