<?php

namespace Database\Factories;

use App\Models\Pass;
use App\Models\PassDetails\DTicket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PassFactory extends Factory
{
    protected $model = Pass::class;

    public function definition(): array
    {
        return [
            'pass_type_id' => 'pass.one.tii.d-ticket',
            'serial_number' => $this->faker->randomNumber(5),
            'authentication_token' => Str::random(60),
            'last_requested_at' => Carbon::now(),
            'details_id' => DTicket::factory(),
            'details_type' => DTicket::class,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
