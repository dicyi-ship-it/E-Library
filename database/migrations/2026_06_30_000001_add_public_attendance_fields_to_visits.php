<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('identity_number')->nullable()->after('guest_name')->index();
            $table->string('visitor_type')->nullable()->after('identity_number');
            $table->string('attendance_source')->default('manual')->after('visitor_type');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['identity_number']);
            $table->dropColumn(['identity_number', 'visitor_type', 'attendance_source']);
        });
    }
};
