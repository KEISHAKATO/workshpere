<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // tie review to an application (so we know the exact engagement)
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();

            // reviewer/reviewee (both users)
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();

            // reviewer role snapshot (optional but helpful for analytics/filtering)
            $table->enum('reviewer_role', ['employer', 'seeker']);

            // the actual feedback
            $table->unsignedTinyInteger('rating'); // 1..5
            $table->string('title', 120)->nullable();
            $table->text('comment')->nullable();

            $table->timestamps();

            // prevent duplicates from the same reviewer for the same application
            $table->unique(['application_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
