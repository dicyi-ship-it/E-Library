<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_book_print_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $book = Book::create([
            'title' => 'Pengantar Sistem Informasi',
            'isbn' => '9780000000001',
            'ddc' => '005.13',
            'call_number' => '005.13 PEN 2026',
            'author' => 'Budi Santoso',
            'publisher' => 'Kampus Press',
            'publication_year' => 2026,
            'language' => 'Indonesia',
            'category' => 'Karya umum, komputer, dan informasi',
            'rack' => 'TI-1',
            'location' => 'Teknologi Informasi',
            'stock_total' => 2,
            'stock_available' => 2,
            'status' => 'available',
        ]);

        $this->actingAs($admin)
            ->get(route('books.print-info', $book))
            ->assertOk()
            ->assertSee('Print Info A4')
            ->assertSee('005.13');

        $this->actingAs($admin)
            ->get(route('books.print-spine', $book))
            ->assertOk()
            ->assertSee('Print Nomor Punggung')
            ->assertSee('C.01');
    }
}
