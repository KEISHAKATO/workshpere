<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // If the table doesn't exist, create it with the expected columns.
        if (! Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();

                // Job this conversation/message belongs to
                $table->foreignId('job_id')
                    ->constrained('job_posts')
                    ->cascadeOnDelete();

                // Who sent the message
                $table->foreignId('sender_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                // The message text
                $table->text('body');

                // Optional: track reads
                $table->timestamp('read_at')->nullable();

                $table->timestamps();

                $table->index(['job_id', 'created_at']);
            });

            return; // done
        }

        // Table exists: make sure the body column is present.
        if (! Schema::hasColumn('messages', 'body')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->text('body')->after('sender_id');
            });
        }

        // Optional: add read_at if you want read receipts
        if (! Schema::hasColumn('messages', 'read_at')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('body');
            });
        }
    }

    public function down(): void
    {
        // If you created the table in this migration, you can drop it on rollback.
        // If the table already existed, we’ll just remove the columns we added.
        if (Schema::hasTable('messages')) {
            // If table was created by this migration, it will have both body + read_at
            // and likely no extra custom columns. To be safe, only drop columns we added.
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'read_at')) {
                    $table->dropColumn('read_at');
                }
                if (Schema::hasColumn('messages', 'body')) {
                    $table->dropColumn('body');
                }
            });
        }
    }
};
