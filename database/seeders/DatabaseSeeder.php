<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Ebook;
use App\Models\Author;
use App\Models\AppSetting;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AppSetting::putMany(AppSetting::defaults());

        User::query()->updateOrCreate([
            'email' => 'admin@elibrary.test',
        ], [
            'name' => 'Admin Perpustakaan',
            'password' => 'password',
            'role' => 'admin',
            'status' => 'active',
            'registered_at' => now(),
        ]);

        User::query()->updateOrCreate([
            'email' => 'member@elibrary.test',
        ], [
            'name' => 'Mahasiswa Demo',
            'password' => 'password',
            'role' => 'member',
            'member_id' => 'LIB-2026-0001',
            'identity_number' => '2301010001',
            'phone' => '081234567890',
            'faculty' => 'Fakultas Teknik',
            'department' => 'Teknik Informatika',
            'study_program' => 'S1 Informatika',
            'level' => 'Mahasiswa',
            'status' => 'active',
            'registered_at' => now(),
        ]);

        Book::query()->updateOrCreate([
            'isbn' => '9786021514900',
        ], [
            'title' => 'Algoritma 1',
            'subtitle' => 'Logika Pemrograman dan Struktur Dasar',
            'ddc' => '005.1',
            'call_number' => '005.1 ALG 2025',
            'author' => 'Tim Dosen Informatika STTI NIIT ITECH',
            'publisher' => 'STTI NIIT ITECH Press',
            'publication_year' => 2025,
            'category' => 'Algoritma dan Pemrograman',
            'rack' => 'TI-01',
            'location' => 'Koleksi Teknologi Informasi',
            'description' => 'Bahan ajar pengantar algoritma, flowchart, pseudocode, percabangan, perulangan, array, dan studi kasus pemrograman dasar.',
            'stock_total' => 12,
            'stock_available' => 9,
            'status' => 'available',
        ]);

        Book::query()->updateOrCreate([
            'isbn' => '9786021514917',
        ], [
            'title' => 'Basis Data',
            'subtitle' => 'Model Relasional, SQL, dan Desain Skema',
            'ddc' => '005.74',
            'call_number' => '005.74 BAS 2025',
            'author' => 'Rina Prasetya',
            'publisher' => 'Informatika Kampus',
            'publication_year' => 2025,
            'category' => 'Basis Data',
            'rack' => 'TI-02',
            'location' => 'Koleksi Teknologi Informasi',
            'description' => 'Referensi praktis untuk normalisasi, ERD, SQL dasar, query lanjutan, dan pengelolaan basis data akademik.',
            'stock_total' => 10,
            'stock_available' => 7,
            'status' => 'available',
        ]);

        Book::query()->updateOrCreate([
            'isbn' => '9786021514924',
        ], [
            'title' => 'Jaringan Komputer',
            'subtitle' => 'Konsep, Protokol, dan Praktik Administrasi',
            'ddc' => '004.6',
            'call_number' => '004.6 JAR 2024',
            'author' => 'Budi Santoso',
            'publisher' => 'Tekno Edukasi',
            'publication_year' => 2024,
            'category' => 'Jaringan Komputer',
            'rack' => 'TI-03',
            'location' => 'Koleksi Teknologi Informasi',
            'description' => 'Pembahasan OSI layer, TCP/IP, subnetting, routing, switching, keamanan jaringan, dan praktik laboratorium.',
            'stock_total' => 8,
            'stock_available' => 6,
            'status' => 'available',
        ]);

        Book::query()->updateOrCreate([
            'isbn' => '9786020633176',
        ], [
            'title' => 'Pengantar Ilmu Perpustakaan',
            'ddc' => '020',
            'call_number' => '020 PEN',
            'author' => 'Sulistyo Basuki',
            'publisher' => 'Gramedia',
            'publication_year' => 2024,
            'category' => 'Ilmu Perpustakaan',
            'rack' => 'A1',
            'location' => 'Koleksi Umum',
            'description' => 'Referensi dasar manajemen perpustakaan dan layanan informasi.',
            'cover_path' => 'covers/books/pengantar-ilmu-perpustakaan.svg',
            'stock_total' => 8,
            'stock_available' => 8,
            'status' => 'available',
        ]);

        Book::query()->updateOrCreate([
            'isbn' => '9780131103627',
        ], [
            'title' => 'The C Programming Language',
            'ddc' => '005.13',
            'call_number' => '005.13 KER',
            'author' => 'Brian W. Kernighan, Dennis M. Ritchie',
            'publisher' => 'Prentice Hall',
            'publication_year' => 1988,
            'category' => 'Pemrograman',
            'rack' => 'C3',
            'location' => 'Teknologi Informasi',
            'description' => 'Buku klasik untuk memahami bahasa C dan dasar pemrograman sistem.',
            'cover_path' => 'covers/books/the-c-programming-language.svg',
            'stock_total' => 5,
            'stock_available' => 5,
            'status' => 'available',
        ]);

        Ebook::query()->updateOrCreate([
            'title' => 'Modul Praktikum Algoritma 1',
        ], [
            'author' => 'Laboratorium Komputasi STTI NIIT ITECH',
            'category' => 'Algoritma dan Pemrograman',
            'description' => 'Ebook PDF berisi latihan praktikum algoritma, contoh pseudocode, dan lembar kerja mingguan.',
            'external_url' => 'https://example.com/modul-praktikum-algoritma-1.pdf',
            'is_active' => true,
        ]);

        Ebook::query()->updateOrCreate([
            'title' => 'Panduan Penulisan Tugas Akhir TI',
        ], [
            'author' => 'Program Studi Teknik Informatika',
            'category' => 'Akademik',
            'description' => 'Panduan format proposal, sitasi, struktur laporan, dan checklist administrasi tugas akhir.',
            'external_url' => 'https://example.com/panduan-tugas-akhir-ti.pdf',
            'is_active' => true,
        ]);

        Ebook::query()->updateOrCreate([
            'title' => 'Panduan Literasi Digital Kampus',
        ], [
            'author' => 'Tim Perpustakaan',
            'category' => 'Literasi Digital',
            'description' => 'Materi pengantar akses database jurnal, sitasi, dan etika akademik.',
            'external_url' => 'https://example.com/panduan-literasi-digital.pdf',
            'is_active' => true,
        ]);

        Book::query()->get()->each(function (Book $book) {
            $updates = [];

            if ($book->author) {
                $updates['author_id'] = Author::firstOrCreate(['name' => $book->author])->id;
            }

            if ($book->publisher) {
                $updates['publisher_id'] = Publisher::firstOrCreate(['name' => $book->publisher])->id;
            }

            if ($updates) {
                $book->update($updates);
            }
        });
    }
}
