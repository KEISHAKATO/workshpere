<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();

            // owner (must be a user with role=employer)
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();

            // core fields
            $table->string('title', 160);
            $table->text('description');
            $table->string('category', 80)->nullable();        // e.g. construction, tailoring
            $table->enum('job_type', ['full_time','part_time','gig','contract'])->default('gig');

            // pay (optional for MVP)
            $table->unsignedInteger('pay_min')->nullable();
            $table->unsignedInteger('pay_max')->nullable();
            $table->char('currency', 3)->default('KES');

            // location
            $table->string('location_city', 120)->nullable();
            $table->string('location_county', 120)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // requirements for matching
            $table->json('required_skills')->nullable();       // ["carpentry","welding"]

            // lifecycle
            $table->enum('status', ['open','closed','paused'])->default('open');
            $table->timestamp('posted_at')->useCurrent();

            $table->timestamps();

            // helpful indexes
            $table->index('employer_id');
            $table->index('status');
            $table->index(['location_county', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
