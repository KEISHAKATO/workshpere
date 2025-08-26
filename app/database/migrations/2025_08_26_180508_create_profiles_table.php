<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            // link to users table (1:1)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();

            // seeker fields
            $table->text('bio')->nullable();
            // store skills as JSON array of strings: ["carpentry","welding"]
            $table->json('skills')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->string('preferred_job_type', 30)->nullable(); // e.g. full_time, part_time, gig, contract
            $table->string('availability', 30)->nullable();        // e.g. immediate, 1_week, flexible

            // employer fields (optional for seekers)
            $table->string('company_name', 160)->nullable();
            $table->string('website', 191)->nullable();

            // location
            $table->string('location_city', 120)->nullable();
            $table->string('location_county', 120)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->timestamps();

            // helpful indexes
            $table->index('location_county');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
