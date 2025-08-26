<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')->constrained('job_posts')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->text('feedback')->nullable();

            $table->timestamps();

            $table->index('reviewee_id');
            $table->index('job_id');

            $table->unique(['job_id', 'reviewer_id']); // one review per job per reviewer
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
