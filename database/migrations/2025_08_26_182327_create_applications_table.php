<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')->constrained('job_posts')->cascadeOnDelete();
            $table->foreignId('seeker_id')->constrained('users')->cascadeOnDelete();

            $table->text('cover_letter')->nullable();
            $table->enum('status', ['pending','accepted','rejected'])->default('pending');

            $table->timestamps();

            $table->unique(['job_id','seeker_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
