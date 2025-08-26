<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // review relates to a job that happened
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();

            // who reviewed whom
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();

            // content
            $table->unsignedTinyInteger('rating'); // 1..5 (enforce range in validation)
            $table->text('feedback')->nullable();

            $table->timestamps();

            // helpful queries: find reviews for a user, or by job
            $table->index('reviewee_id');
            $table->index('job_id');

            // optional: prevent duplicate review from same reviewer for same job
            $table->unique(['job_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
