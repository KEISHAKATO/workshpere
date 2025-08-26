<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->faker = FakerFactory::create('en_US');
        //  Employers 
        $employers = collect();
        for ($i=1; $i<=5; $i++) {
            $email = "employer{$i}@worksphere.test";
            $user = \App\Models\User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => "Employer {$i}",
                    'password' => Hash::make('password'),
                ]
            );
            // set role safely (not mass assignment)
            if ($user->role !== 'employer') { $user->role = 'employer'; $user->save(); }

            // profile
            $profile = $user->profile()->updateOrCreate([], \Database\Factories\ProfileFactory::new()->definition());
            $profile->company_name = $profile->company_name ?: $this->faker->company(); // realistic company name
            $profile->save();
            $employers->push($user);
        }

        //  Seekers 
        $seekers = collect();
        for ($i=1; $i<=12; $i++) {
            $email = "seeker{$i}@worksphere.test";
            $user = \App\Models\User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => "Seeker {$i}",
                    'password' => Hash::make('password'),
                ]
            );
            if ($user->role !== 'seeker') { $user->role = 'seeker'; $user->save(); }

            $user->profile()->updateOrCreate([], \Database\Factories\ProfileFactory::new()->definition());
            $seekers->push($user);
        }

        //  Jobs per employer 
        $jobs = collect();
        foreach ($employers as $employer) {
            $count = rand(2, 3); // 2-3 jobs each
            for ($j=0; $j<$count; $j++) {
                $job = $employer->jobs()->create(\Database\Factories\JobFactory::new()->definition());
                $jobs->push($job);
            }
        }

        //  Applications: each job gets 3-5 applications from random seekers 
        foreach ($jobs as $job) {
            $applicants = $seekers->shuffle()->take(rand(3,5));
            foreach ($applicants as $seeker) {
                \App\Models\Application::firstOrCreate(
                    ['job_id' => $job->id, 'seeker_id' => $seeker->id],
                    \Database\Factories\ApplicationFactory::new()->definition()
                );
            }
        }

        //  Messages: 1-2 messages per application from employer to seeker 
        foreach (\App\Models\Application::inRandomOrder()->take(20)->get() as $app) {
            \App\Models\Message::updateOrCreate(
                [
                    'job_id'      => $app->job_id,
                    'sender_id'   => $app->job->employer_id,
                    'receiver_id' => $app->seeker_id,
                    'content'     => 'Thanks for applying—can you start next week?',
                ],
                []
            );
        }

        //  Reviews: add a few finished-job reviews (random subset) 
        $jobsForReview = \App\Models\Job::inRandomOrder()->take(10)->get();
        foreach ($jobsForReview as $job) {
            $application = $job->applications()->inRandomOrder()->first();
            if (!$application) {
                continue; // no applicants for this job
            }

            \App\Models\Review::updateOrCreate(
                [
                    'job_id'      => $job->id,
                    'reviewer_id' => $job->employer_id,
                ],
                // values to set/update
                array_merge(
                    \Database\Factories\ReviewFactory::new()->definition(),
                    ['reviewee_id' => $application->seeker_id]
                )
            );
        }
    }
}
