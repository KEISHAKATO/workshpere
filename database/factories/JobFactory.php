<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $jobTypes = ['full_time','part_time','gig','contract']; // <= matches DB enum

        $skillsPool = [
            'PHP','Laravel','MySQL','Vue','React','Node.js','Docker','AWS','Redis','Git',
            'REST APIs','CI/CD','PostgreSQL','Kotlin','Swift'
        ];

        $reqSkills = $this->faker->randomElements($skillsPool, $this->faker->numberBetween(3, 6));

        return [
            'employer_id'     => User::factory()->state(['role' => 'employer']),
            'title'           => $this->faker->jobTitle(),
            'description'     => $this->faker->paragraphs(4, true),
            'category'        => $this->faker->randomElement([
                'Technology','Education','Healthcare','Finance','Retail','Construction','Hospitality'
            ]),
            'job_type'        => $this->faker->randomElement($jobTypes),
            'pay_min'         => $this->faker->numberBetween(50_000, 200_000),
            'pay_max'         => $this->faker->numberBetween(200_001, 450_000),
            'currency'        => 'KES',
            'location_city'   => $this->faker->randomElement(['Nairobi','Mombasa','Kisumu','Nakuru']),
            'location_county' => $this->faker->randomElement(['Nairobi County','Mombasa County','Kisumu County','Nakuru County']),
            'lat'             => $this->faker->randomFloat(7, -1.5, 1.5),
            'lng'             => $this->faker->randomFloat(7, 36.5, 41.0),
            'required_skills' => array_values($reqSkills),
            'status'          => 'open',
            'posted_at'       => $this->faker->dateTimeBetween('-60 days', 'now'),
        ];
    }
}
