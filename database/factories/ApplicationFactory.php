<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            // Prefer passing explicit job_id & seeker_id via ->for()/state() in seeder.
            'job_id'      => Job::factory(), // will be overridden in seeder when needed
            'seeker_id'   => User::factory()->state(['role' => 'seeker']),
            'cover_letter'=> $this->faker->boolean(70) ? $this->faker->paragraphs(2, true) : null,
            'status'      => $this->faker->randomElement(['pending','accepted','rejected']),
        ];
    }

    public function pending(): self   { return $this->state(fn() => ['status' => 'pending']); }
    public function accepted(): self  { return $this->state(fn() => ['status' => 'accepted']); }
    public function rejected(): self  { return $this->state(fn() => ['status' => 'rejected']); }
}
