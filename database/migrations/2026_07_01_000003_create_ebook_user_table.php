<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ebook_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ebook_id')->constrained()->cascadeOnDelete();
            $table->timestamp('first_accessed_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();
            $table->unsignedInteger('read_count')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'ebook_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ebook_user');
    }
};
