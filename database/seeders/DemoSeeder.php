<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\Message;
use App\Models\Review;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = FakerFactory::create('en_US');

        // Employers
        $employers = collect();
        for ($i = 1; $i <= 5; $i++) {
            $email = "employer{$i}@worksphere.test";
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'              => "Employer {$i}",
                    'password'          => Hash::make('password'),
                    'role'              => 'employer',
                    'email_verified_at' => now(),
                ]
            );
            if ($user->role !== 'employer') { $user->role = 'employer'; $user->save(); }

            $user->profile()->updateOrCreate([], [
                'company_name' => $faker->company(),
                'bio'          => $faker->sentence(),
            ]);

            $employers->push($user);
        }

        // Seekers
        $seekers = collect();
        for ($i = 1; $i <= 12; $i++) {
            $email = "seeker{$i}@worksphere.test";
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'              => "Seeker {$i}",
                    'password'          => Hash::make('password'),
                    'role'              => 'seeker',
                    'email_verified_at' => now(),
                ]
            );
            if ($user->role !== 'seeker') { $user->role = 'seeker'; $user->save(); }

            $user->profile()->updateOrCreate([], [
                'bio' => $faker->sentence(),
            ]);

            $seekers->push($user);
        }

        // Jobs per employer (factory only)
        $jobs = collect();
        foreach ($employers as $employer) {
            Job::factory()
                ->count(rand(2, 3))
                ->for($employer, 'employer')
                ->create()
                ->each(static function ($job) use ($jobs) {
                    $jobs->push($job);
                });
        }

        // Applications
        foreach ($jobs as $job) {
            $applicants = $seekers->shuffle()->take(rand(3, 5));
            foreach ($applicants as $seeker) {
                Application::firstOrCreate(
                    ['job_id' => $job->id, 'seeker_id' => $seeker->id],
                    ['status' => 'pending']
                );
            }
        }

        // Messages (uses 'body')
        foreach (Application::inRandomOrder()->take(20)->get() as $app) {
            Message::updateOrCreate(
                [
                    'job_id'      => $app->job_id,
                    'sender_id'   => $app->job->employer_id,
                    'receiver_id' => $app->seeker_id,
                ],
                [
                    'body' => 'Thanks for applying — can you start next week?',
                ]
            );
        }

        // Reviews (schema: job_id, reviewer_id, reviewee_id, rating, feedback)
        if (class_exists(Review::class)) {
            $jobsForReview = Job::inRandomOrder()->take(10)->get();
            foreach ($jobsForReview as $job) {
                $application = $job->applications()->inRandomOrder()->first();
                if (!$application) continue;

                Review::updateOrCreate(
                    [
                        'job_id'      => $job->id,
                        'reviewer_id' => $job->employer_id,
                    ],
                    [
                        'reviewee_id' => $application->seeker_id,
                        'rating'      => rand(3, 5),
                        'feedback'    => 'Great work! ' . $faker->sentence(),
                    ]
                );
            }
        }
    }
}
