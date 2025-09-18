<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RelationshipTestSeeder extends Seeder
{
    public function run(): void
    {
        // USERS (safe if run multiple times)
        $employer = \App\Models\User::firstOrCreate(
            ['email' => 'employer@test.local'],
            [
                'name' => 'Sample Employer',
                'password' => Hash::make('password'),
                'role' => 'employer',
            ]
        );

        $seeker = \App\Models\User::firstOrCreate(
            ['email' => 'seeker@test.local'],
            [
                'name' => 'Sample Seeker',
                'password' => Hash::make('password'),
                'role' => 'seeker',
            ]
        );

        // Ensure roles are correct (in case they existed from before)
        if ($employer->role !== 'employer') { $employer->role = 'employer'; $employer->save(); }
        if ($seeker->role !== 'seeker')     { $seeker->role = 'seeker';     $seeker->save();     }

        // PROFILES (one per user)
        $employer->profile()->updateOrCreate(
            [], // unique by user_id via relation
            [
                'company_name'    => 'Acme Co',
                'location_city'   => 'Nairobi',
                'location_county' => 'Nairobi',
            ]
        );

        $seeker->profile()->updateOrCreate(
            [],
            [
                'skills'            => ['carpentry','welding'],
                'experience_years'  => 2,
                'location_city'     => 'Nairobi',
                'location_county'   => 'Nairobi',
            ]
        );

        // JOB (owned by employer)
        $job = $employer->jobs()->firstOrCreate(
            ['title' => 'Carpenter needed'], // "natural key" for demo purposes
            [
                'description'     => 'Build custom shelves.',
                'category'        => 'construction',
                'job_type'        => 'gig',
                'location_city'   => 'Nairobi',
                'location_county' => 'Nairobi',
                'required_skills' => ['carpentry'],
                'status'          => 'open',
            ]
        );

        // APPLICATION (unique by job_id + seeker_id)
        \App\Models\Application::firstOrCreate(
            ['job_id' => $job->id, 'seeker_id' => $seeker->id],
            ['cover_letter' => 'I have 2 years experience in carpentry.']
        );

        \App\Models\Message::updateOrCreate(
            [
                'job_id'     => $job->id,
                'sender_id'  => $employer->id,
                'receiver_id'=> $seeker->id,
                'body'    => 'Can you start next week?',
            ],
            [] // no extra fields to update
        );

        \App\Models\Review::firstOrCreate(
            [
                'job_id'      => $job->id,
                'reviewer_id' => $employer->id,
                'reviewee_id' => $seeker->id,
            ],
            [
                'rating'   => 5,
                'feedback' => 'Excellent work!',
            ]
        );
    }
}
