<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'is_active'))  $t->boolean('is_active')->default(true)->after('password');
            if (!Schema::hasColumn('users', 'is_flagged')) $t->boolean('is_flagged')->default(false)->after('is_active');
        });

        Schema::table('job_posts', function (Blueprint $t) {
            if (!Schema::hasColumn('job_posts', 'is_flagged')) $t->boolean('is_flagged')->default(false)->after('status');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            if (Schema::hasColumn('users', 'is_flagged')) $t->dropColumn('is_flagged');
            if (Schema::hasColumn('users', 'is_active'))  $t->dropColumn('is_active');
        });

        Schema::table('job_posts', function (Blueprint $t) {
            if (Schema::hasColumn('job_posts', 'is_flagged')) $t->dropColumn('is_flagged');
        });
    }
};
