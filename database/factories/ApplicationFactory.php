<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cover_letter' => $this->faker->randomElement([
                'I am available immediately and can start this week.',
                'I have relevant experience and deliver quality results.',
                'I am reliable, punctual and ready to work.',
                'I have handled similar tasks and can support your project.',
            ]),
            'status'       => 'pending',
        ];
    }
}
