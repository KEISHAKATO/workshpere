<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('messages', 'receiver_id')) {
            Schema::table('messages', function (Blueprint $table) {
                // receiver of the message (a user)
                $table->foreignId('receiver_id')
                    ->nullable() // keep nullable for safety; controller will set it
                    ->after('sender_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                // helpful index for inbox-type queries
                $table->index(['job_id', 'sender_id', 'receiver_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('messages', 'receiver_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropIndex(['job_id', 'sender_id', 'receiver_id']);
                $table->dropConstrainedForeignId('receiver_id'); // drops FK and column
            });
        }
    }
};
