<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('app_settings')->insert([
            ['key' => 'app_name', 'value' => 'E-Library', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'institution_name', 'value' => 'STTI NIIT I-Tech', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'library_name', 'value' => 'Perpustakaan Digital', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'logo_text', 'value' => 'IT', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
