<?php

namespace Tests\Feature;

use App\Models\User;
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
}
