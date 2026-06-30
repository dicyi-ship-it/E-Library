<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function access()
    {
        return view('auth.access');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials + ['status' => 'active'], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(auth()->user()->isAdmin() ? route('admin.dashboard') : route('ebooks.reader'));
        }

        return back()->withErrors(['email' => 'Email atau password tidak cocok, atau akun belum aktif.'])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'identity_number' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'faculty' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'study_program' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:100'],
        ]);

        $user = User::create($data + [
            'role' => 'member',
            'member_id' => 'LIB-'.now()->format('YmdHis'),
            'status' => 'active',
            'registered_at' => today(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('ebooks.reader')->with('status', 'Registrasi berhasil. Silakan baca atau unduh ebook.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
