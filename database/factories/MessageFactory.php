<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content'  => $this->faker->randomElement([
                'Thank you for applying. Can you start next week?',
                'Please share your availability for a brief call.',
                'Kindly confirm your expected daily rate.',
                'We will review your application and get back to you.',
            ]),
            'read_at'  => null,
        ];
    }
}
