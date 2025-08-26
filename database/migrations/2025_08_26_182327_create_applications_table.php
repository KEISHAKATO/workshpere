<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // links
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            // seeker is a user with role=seeker (enforced by app logic)
            $table->foreignId('seeker_id')->constrained('users')->cascadeOnDelete();

            // application content
            $table->text('cover_letter')->nullable();

            // workflow status
            $table->enum('status', ['pending','accepted','rejected'])->default('pending');

            $table->timestamps();

            // one application per seeker per job
            $table->unique(['job_id','seeker_id']);

            // helpful indexes
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
