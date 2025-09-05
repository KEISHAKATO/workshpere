<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Database\Seeder;

class SyntheticMlSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Employers (create a few)
        $employers = User::factory()
            ->count(5)
            ->state(['role' => 'employer'])
            ->create();

        // 2) Jobs for those employers (via JobFactory which already creates employer if not set)
        //    but here we attach explicitly to existing employers to avoid too many users
        $jobs = collect();
        foreach ($employers as $employer) {
            $jobs = $jobs->merge(
                Job::factory()
                    ->count(3)
                    ->state(['employer_id' => $employer->id]) // ensure owner is the employer
                    ->create()
            );
        }

        // 3) Seekers
        $seekers = User::factory()
            ->count(20)
            ->state(['role' => 'seeker'])
            ->create();

        // 4) Applications (mix of pending/accepted/rejected)
        foreach ($jobs as $job) {
            // pick 3–7 random seekers to apply to this job
            $candidates = $seekers->random(rand(3, 7));
            foreach ($candidates as $seeker) {
                // avoid duplicates
                if (Application::where('job_id', $job->id)->where('seeker_id', $seeker->id)->exists()) {
                    continue;
                }

                $status = collect(['pending','accepted','rejected'])->random();

                Application::factory()
                    ->state([
                        'job_id'    => $job->id,
                        'seeker_id' => $seeker->id,
                        'status'    => $status,
                    ])->create();
            }
        }

        $this->command->info('Synthetic ML data seeded: employers, jobs, seekers, applications.');
    }
}
