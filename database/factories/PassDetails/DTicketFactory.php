<?php

namespace Database\Factories\PassDetails;

use App\Models\PassDetails\DTicket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DTicketFactory extends Factory
{
    protected $model = DTicket::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'valid_in' => $this->faker->dateTimeBetween('-1 year'),
            'barcode' => Str::random(720),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
