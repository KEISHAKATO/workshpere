<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            // 1:1 with users
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();

            // Seeker fields
            $table->string('bio', 160)->nullable();      // short one-liner
            $table->text('about')->nullable();           // longer paragraph
            $table->json('skills')->nullable();          // ["carpentry","welding"]
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->string('preferred_job_type', 30)->nullable(); // full_time/part_time/gig/contract
            $table->string('availability', 30)->nullable();        // immediate/1_week/flexible

            // Employer fields (optional for seekers)
            $table->string('company_name', 160)->nullable();
            $table->string('website', 191)->nullable();

            // Location (both roles)
            $table->string('location_city', 120)->nullable();
            $table->string('location_county', 120)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->timestamps();

            $table->index('location_county');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
