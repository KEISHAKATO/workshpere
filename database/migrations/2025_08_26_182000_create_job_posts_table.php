<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();

            $table->string('title', 160);
            $table->text('description');
            $table->string('category', 80)->nullable();
            $table->enum('job_type', ['full_time','part_time','gig','contract'])->default('gig');

            $table->unsignedInteger('pay_min')->nullable();
            $table->unsignedInteger('pay_max')->nullable();
            $table->char('currency', 3)->default('KES');

            $table->string('location_city', 120)->nullable();
            $table->string('location_county', 120)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->json('required_skills')->nullable();

            $table->enum('status', ['open','closed','paused'])->default('open');
            $table->timestamp('posted_at')->useCurrent();

            $table->timestamps();

            $table->index('employer_id');
            $table->index('status');
            $table->index(['location_county', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
