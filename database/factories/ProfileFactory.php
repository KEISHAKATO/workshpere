<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        $skillsPool = ['PHP','Laravel','Node.js','React','Vue','Docker','AWS','SQL','Kotlin','Swift','CI/CD','GraphQL','Redis','PostgreSQL','MongoDB'];
        shuffle($skillsPool);
        $skills = array_slice($skillsPool, 0, rand(3,7));

        return [
            'bio'               => $this->faker->sentence(),
            'about'             => $this->faker->paragraph(),
            'skills'            => $skills,
            'experience_years'  => $this->faker->numberBetween(0, 12),
            'preferred_job_type'=> $this->faker->randomElement(['full_time','part_time','contract','remote']),
            'availability'      => $this->faker->randomElement(['immediately','2_weeks','1_month']),
            'location_city'     => $this->faker->randomElement(['Nairobi','Mombasa','Kisumu','Nakuru']),
            'location_county'   => $this->faker->randomElement(['Nairobi County','Mombasa County','Kisumu County','Nakuru County']),
            'lat'               => $this->faker->randomFloat(7,-1,1),
            'lng'               => $this->faker->randomFloat(7,36,40),
            // employer-only fields (may be null for seekers)
            'company_name'      => null,
            'website'           => null,
        ];
    }
}
