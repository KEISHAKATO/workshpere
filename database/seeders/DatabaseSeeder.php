<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Make specific users idempotently (safe to re-run) ---
        // Test user (example)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'              => 'Test User',
                'password'          => Hash::make('password'),
                'role'              => 'seeker',   // adjust if needed
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // (Optional) Admin user
        User::firstOrCreate(
            ['email' => 'admin@worksphere.test'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );

        // --- Run your other seeders in order ---
        $this->call([
            DemoSeeder::class,
            RelationshipTestSeeder::class,
            SyntheticMlSeeder::class,
        ]);
    }
}
