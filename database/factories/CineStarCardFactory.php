<?php

namespace Database\Factories;

use App\Models\PassDetails\CineStarCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CineStarCardFactory extends Factory
{
    protected $model = CineStarCard::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'customer_number' => $this->faker->word(),
            'premium_points' => $this->faker->randomFloat(),
            'regular_cinema_id' => CineStarCard\Cinema::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
