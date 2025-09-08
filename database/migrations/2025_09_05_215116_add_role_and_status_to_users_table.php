<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // role
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['seeker','employer','admin'])->default('seeker')->after('password');
            }

            // is_active: true means allowed to use the app
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }

            // optional, useful for admin UI
            if (!Schema::hasColumn('users', 'is_flagged')) {
                $table->boolean('is_flagged')->default(false)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_flagged')) {
                $table->dropColumn('is_flagged');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
