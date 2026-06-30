<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('member')->after('password');
            $table->string('member_id')->nullable()->unique()->after('role');
            $table->string('identity_number')->nullable()->after('member_id');
            $table->string('phone')->nullable()->after('identity_number');
            $table->string('faculty')->nullable()->after('phone');
            $table->string('department')->nullable()->after('faculty');
            $table->string('study_program')->nullable()->after('department');
            $table->string('level')->nullable()->after('study_program');
            $table->string('status')->default('active')->after('level');
            $table->date('registered_at')->nullable()->after('status');
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->string('ddc')->index();
            $table->string('call_number')->nullable()->index();
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('edition')->nullable();
            $table->string('language')->default('Indonesia');
            $table->string('category')->nullable()->index();
            $table->string('rack')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->unsignedInteger('stock_total')->default(1);
            $table->unsignedInteger('stock_available')->default(1);
            $table->string('status')->default('available');
            $table->timestamps();
        });

        Schema::create('ebooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('category')->nullable()->index();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('cover_path')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('purpose')->default('Membaca');
            $table->timestamp('check_in_at');
            $table->timestamp('check_out_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->string('loan_code')->unique();
            $table->date('borrowed_at');
            $table->date('due_at');
            $table->date('returned_at')->nullable();
            $table->string('status')->default('borrowed')->index();
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('ebooks');
        Schema::dropIfExists('books');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['member_id']);
            $table->dropColumn([
                'role',
                'member_id',
                'identity_number',
                'phone',
                'faculty',
                'department',
                'study_program',
                'level',
                'status',
                'registered_at',
            ]);
        });
    }
};
