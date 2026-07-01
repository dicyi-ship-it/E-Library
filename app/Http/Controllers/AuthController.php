<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function access(Request $request)
    {
        if ($request->user()) {
            return $request->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('member.card');
        }

        return view('auth.access', [
            'loginCaptcha' => $this->captcha($request, 'login'),
            'registerCaptcha' => $this->captcha($request, 'register'),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'login_captcha_answer' => ['required', 'integer'],
        ]);

        $this->validateCaptcha($request, 'login', 'login_captcha_answer');
        unset($credentials['login_captcha_answer']);

        if (Auth::attempt($credentials + ['status' => 'active'], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return auth()->user()->isAdmin()
                ? redirect()->intended(route('admin.dashboard'))
                : redirect()->intended(route('member.card'));
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
            'register_captcha_answer' => ['required', 'integer'],
        ]);

        $this->validateCaptcha($request, 'register', 'register_captcha_answer');
        unset($data['register_captcha_answer']);

        $user = User::create($data + [
            'role' => 'member',
            'member_id' => 'LIB-'.now()->format('YmdHis'),
            'status' => 'active',
            'registered_at' => today(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('member.card')->with('status', 'Registrasi berhasil. Kartu anggota perpustakaan Anda sudah aktif.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    private function captcha(Request $request, string $name): array
    {
        $key = $this->captchaKey($name);

        if (! $request->session()->has($key)) {
            return $this->refreshCaptcha($request, $name);
        }

        return $request->session()->get($key);
    }

    private function refreshCaptcha(Request $request, string $name): array
    {
        $firstNumber = random_int(2, 9);
        $secondNumber = random_int(1, 9);
        $captcha = [
            'question' => "{$firstNumber} + {$secondNumber}",
            'answer' => $firstNumber + $secondNumber,
        ];

        $request->session()->put($this->captchaKey($name), $captcha);

        return $captcha;
    }

    private function validateCaptcha(Request $request, string $name, string $field): void
    {
        $captcha = $request->session()->get($this->captchaKey($name));

        if (! $captcha || (int) $request->input($field) !== (int) $captcha['answer']) {
            $this->refreshCaptcha($request, $name);

            throw ValidationException::withMessages([
                $field => 'Jawaban captcha penjumlahan tidak tepat.',
            ]);
        }

        $this->refreshCaptcha($request, $name);
    }

    private function captchaKey(string $name): string
    {
        return "auth_captcha_{$name}";
    }
}
