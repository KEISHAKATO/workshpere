<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Seeker-side fields
            if (!Schema::hasColumn('profiles', 'about'))            $table->text('about')->nullable()->after('user_id');
            if (!Schema::hasColumn('profiles', 'skills'))           $table->json('skills')->nullable()->after('about');
            if (!Schema::hasColumn('profiles', 'experience_years')) $table->unsignedTinyInteger('experience_years')->nullable()->after('skills');
            if (!Schema::hasColumn('profiles', 'location_city'))    $table->string('location_city', 120)->nullable()->after('experience_years');
            if (!Schema::hasColumn('profiles', 'location_county'))  $table->string('location_county', 120)->nullable()->after('location_city');
            if (!Schema::hasColumn('profiles', 'lat'))              $table->decimal('lat', 10, 7)->nullable()->after('location_county');
            if (!Schema::hasColumn('profiles', 'lng'))              $table->decimal('lng', 10, 7)->nullable()->after('lat');

            // Employer-side fields
            if (!Schema::hasColumn('profiles', 'company_name'))        $table->string('company_name', 160)->nullable()->after('lng');
            if (!Schema::hasColumn('profiles', 'company_website'))     $table->string('company_website', 200)->nullable()->after('company_name');
            if (!Schema::hasColumn('profiles', 'company_location'))    $table->string('company_location', 160)->nullable()->after('company_website');
            if (!Schema::hasColumn('profiles', 'company_description')) $table->text('company_description')->nullable()->after('company_location');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            // Drop in safe order (wrap each in hasColumn for safety)
            if (Schema::hasColumn('profiles', 'company_description')) $table->dropColumn('company_description');
            if (Schema::hasColumn('profiles', 'company_location'))    $table->dropColumn('company_location');
            if (Schema::hasColumn('profiles', 'company_website'))     $table->dropColumn('company_website');
            if (Schema::hasColumn('profiles', 'company_name'))        $table->dropColumn('company_name');

            if (Schema::hasColumn('profiles', 'lng'))              $table->dropColumn('lng');
            if (Schema::hasColumn('profiles', 'lat'))              $table->dropColumn('lat');
            if (Schema::hasColumn('profiles', 'location_county'))  $table->dropColumn('location_county');
            if (Schema::hasColumn('profiles', 'location_city'))    $table->dropColumn('location_city');
            if (Schema::hasColumn('profiles', 'experience_years')) $table->dropColumn('experience_years');
            if (Schema::hasColumn('profiles', 'skills'))           $table->dropColumn('skills');
            if (Schema::hasColumn('profiles', 'about'))            $table->dropColumn('about');
        });
    }
};
