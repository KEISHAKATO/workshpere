<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // conversation is always tied to a specific job posting
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();

            // participants (both are users)
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();

            // content
            $table->text('content');

            // read receipts
            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            // helpful indexes for fast inbox queries
            $table->index(['receiver_id', 'read_at']);
            $table->index(['job_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
