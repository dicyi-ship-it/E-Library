<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ebook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_login_redirects_to_library_card_with_valid_captcha(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'status' => 'active',
            'identity_number' => '2301010001',
            'member_id' => 'LIB-2026-0001',
            'level' => 'Mahasiswa',
        ]);

        $this
            ->withSession(['auth_captcha_login' => ['question' => '4 + 5', 'answer' => 9]])
            ->post(route('login'), [
                'email' => $member->email,
                'password' => 'password',
                'login_captcha_answer' => 9,
            ])
            ->assertRedirect(route('member.card'));

        $this
            ->actingAs($member)
            ->get(route('member.card'))
            ->assertOk()
            ->assertSee('Kartu Anggota Perpustakaan')
            ->assertSee('2301010001');
    }

    public function test_login_rejects_invalid_captcha(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'status' => 'active',
        ]);

        $this
            ->withSession(['auth_captcha_login' => ['question' => '4 + 5', 'answer' => 9]])
            ->post(route('login'), [
                'email' => $member->email,
                'password' => 'password',
                'login_captcha_answer' => 8,
            ])
            ->assertSessionHasErrors('login_captcha_answer');

        $this->assertGuest();
    }

    public function test_member_ebook_reader_contains_only_accessed_ebooks_and_can_remove_them(): void
    {
        $member = User::factory()->create([
            'role' => 'member',
            'status' => 'active',
        ]);

        $ebook = Ebook::create([
            'title' => 'Modul Algoritma 1',
            'author' => 'Laboratorium Komputasi',
            'category' => 'Algoritma',
            'description' => 'Materi praktikum algoritma.',
            'external_url' => 'https://example.com/modul-algoritma-1.pdf',
            'is_active' => true,
        ]);

        $otherEbook = Ebook::create([
            'title' => 'Panduan Tugas Akhir',
            'author' => 'Program Studi',
            'category' => 'Akademik',
            'description' => 'Panduan penulisan.',
            'external_url' => 'https://example.com/panduan-ta.pdf',
            'is_active' => true,
        ]);

        $this
            ->actingAs($member)
            ->get(route('ebooks.read', $ebook))
            ->assertOk()
            ->assertSee('Modul Algoritma 1');

        $this->assertDatabaseHas('ebook_user', [
            'user_id' => $member->id,
            'ebook_id' => $ebook->id,
            'read_count' => 1,
        ]);

        $this
            ->actingAs($member)
            ->get(route('ebooks.reader'))
            ->assertOk()
            ->assertSee('Modul Algoritma 1')
            ->assertDontSee('Panduan Tugas Akhir');

        $this
            ->actingAs($member)
            ->delete(route('ebooks.reader.remove', $ebook))
            ->assertRedirect(route('ebooks.reader'));

        $this->assertDatabaseMissing('ebook_user', [
            'user_id' => $member->id,
            'ebook_id' => $ebook->id,
        ]);

        $this->assertDatabaseMissing('ebook_user', [
            'user_id' => $member->id,
            'ebook_id' => $otherEbook->id,
        ]);
    }
}
