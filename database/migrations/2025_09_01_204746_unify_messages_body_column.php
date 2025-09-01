<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // If table had only `content`, rename it to `body`
        if (Schema::hasColumn('messages', 'content') && !Schema::hasColumn('messages', 'body')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->renameColumn('content', 'body');
            });
        }

        // If table has both `body` and `content`, copy content -> body where body is NULL
        if (Schema::hasColumn('messages', 'content') && Schema::hasColumn('messages', 'body')) {
            // ensure `body` is nullable during backfill to avoid issues
            Schema::table('messages', function (Blueprint $table) {
                $table->text('body')->nullable()->change();
            });

            DB::statement('UPDATE messages SET body = COALESCE(body, content)');

            // Now we can safely drop `content`
            Schema::table('messages', function (Blueprint $table) {
                $table->dropColumn('content');
            });

            // Optionally make body NOT NULL if you want
            Schema::table('messages', function (Blueprint $table) {
                $table->text('body')->nullable(false)->change();
            });
        }

        // Make sure receiver_id is NOT NULL (you already supply it)
        if (Schema::hasColumn('messages', 'receiver_id')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->unsignedBigInteger('receiver_id')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        // Recreate `content` (nullable) and copy back from `body`, then (optionally) keep both
        if (!Schema::hasColumn('messages', 'content') && Schema::hasColumn('messages', 'body')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->text('content')->nullable()->after('receiver_id');
            });

            DB::statement('UPDATE messages SET content = body');
        }
    }
};
