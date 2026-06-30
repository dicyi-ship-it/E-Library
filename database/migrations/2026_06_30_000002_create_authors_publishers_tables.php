<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('author')->constrained()->nullOnDelete();
            $table->foreignId('publisher_id')->nullable()->after('publisher')->constrained()->nullOnDelete();
        });

        DB::table('books')->select('id', 'author', 'publisher')->orderBy('id')->chunk(100, function ($books) {
            foreach ($books as $book) {
                $updates = [];

                if ($authorName = trim((string) $book->author)) {
                    $updates['author_id'] = $this->indexedId('authors', $authorName);
                }

                if ($publisherName = trim((string) $book->publisher)) {
                    $updates['publisher_id'] = $this->indexedId('publishers', $publisherName);
                }

                if ($updates) {
                    DB::table('books')->where('id', $book->id)->update($updates);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropConstrainedForeignId('publisher_id');
            $table->dropConstrainedForeignId('author_id');
        });

        Schema::dropIfExists('publishers');
        Schema::dropIfExists('authors');
    }

    private function indexedId(string $table, string $name): int
    {
        $id = DB::table($table)->where('name', $name)->value('id');

        if ($id) {
            return (int) $id;
        }

        return (int) DB::table($table)->insertGetId([
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
