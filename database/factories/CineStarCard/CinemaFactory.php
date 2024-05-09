<?php

namespace Database\Factories\CineStarCard;

use Illuminate\Database\Eloquent\Factories\Factory;

class CinemaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'CineStar '.$this->faker->city(),
            'latitude' => $this->faker->latitude(47, 54),
            'longitude' => $this->faker->longitude(6, 14),
        ];
    }
}
