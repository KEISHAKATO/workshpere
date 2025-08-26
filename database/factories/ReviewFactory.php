<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rating'   => $this->faker->numberBetween(4, 5),
            'feedback' => $this->faker->randomElement([
                'Great work and very professional.',
                'Punctual, cooperative and delivered quality.',
                'Good communication and reliable results.',
                'Clear, efficient and easy to work with.',
            ]),
        ];
    }
}
