@extends('layouts.app')

@section('title', 'Masuk Anggota')

@section('content')
    <section class="grid gap-8 lg:grid-cols-[.85fr_1.15fr]">
        <div class="rounded-lg bg-slate-950 p-6 text-white">
            <p class="text-sm font-semibold text-sky-200">{{ $appSettings['library_name'] }}</p>
            <h1 class="mt-3 text-4xl font-bold leading-tight">Masuk dan registrasi anggota perpustakaan.</h1>
            <p class="mt-4 leading-7 text-slate-300">
                Gunakan akun anggota untuk membaca ebook, mengunduh koleksi digital, dan mengakses layanan perpustakaan yang tersedia.
            </p>
            <div class="mt-6 grid gap-3">
                <a class="rounded-md border border-white/15 bg-white/10 p-4 transition hover:bg-white/15" href="{{ route('catalog.books.index') }}">
                    <span class="text-sm text-sky-100">Katalog buku</span>
                    <strong class="mt-1 block text-lg">Telusuri koleksi fisik</strong>
                </a>
                <a class="rounded-md border border-white/15 bg-white/10 p-4 transition hover:bg-white/15" href="{{ route('catalog.ebooks.index') }}">
                    <span class="text-sm text-sky-100">Katalog ebook</span>
                    <strong class="mt-1 block text-lg">Akses koleksi digital</strong>
                </a>
            </div>
        </div>

        <div class="grid gap-5">
            <form method="POST" action="{{ route('login') }}" class="panel grid gap-4">
                @csrf
                <h2 class="section-title">Masuk Anggota</h2>
                <input class="input min-h-12" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                <input class="input min-h-12" type="password" name="password" placeholder="Password" required>
                <label>
                    <span class="mb-1 block text-sm font-semibold text-slate-700">Captcha: {{ $loginCaptcha['question'] }} = ?</span>
                    <input class="input min-h-12" type="number" name="login_captcha_answer" placeholder="Jawaban penjumlahan" required>
                </label>
                <button class="btn-primary min-h-12" type="submit">Masuk</button>
            </form>

            <form method="POST" action="{{ route('register') }}" class="panel grid gap-3">
                @csrf
                <h2 class="section-title">Registrasi Member</h2>
                <div class="grid gap-3 sm:grid-cols-2">
                    <input class="input" name="name" placeholder="Nama lengkap" value="{{ old('name') }}" required>
                    <input class="input" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    <input class="input" name="identity_number" placeholder="NIM/NIS/NIDN/NUPTK" value="{{ old('identity_number') }}" required>
                    <input class="input" name="phone" placeholder="No. telepon" value="{{ old('phone') }}">
                    <input class="input" name="faculty" placeholder="Fakultas/Unit" value="{{ old('faculty') }}" required>
                    <input class="input" name="study_program" placeholder="Program studi/unit" value="{{ old('study_program') }}" required>
                    <select class="input" name="level" required>
                        <option value="">Jenis anggota</option>
                        @foreach(['Mahasiswa', 'Dosen', 'Tenaga Kependidikan', 'Siswa', 'Guru', 'Peneliti'] as $level)
                            <option @selected(old('level') === $level)>{{ $level }}</option>
                        @endforeach
                    </select>
                    <input class="input" type="password" name="password" placeholder="Password min. 8 karakter" required>
                    <input class="input sm:col-span-2" type="password" name="password_confirmation" placeholder="Konfirmasi password" required>
                    <label class="sm:col-span-2">
                        <span class="mb-1 block text-sm font-semibold text-slate-700">Captcha: {{ $registerCaptcha['question'] }} = ?</span>
                        <input class="input" type="number" name="register_captcha_answer" placeholder="Jawaban penjumlahan" required>
                    </label>
                </div>
                <button class="btn-primary w-fit" type="submit">Daftar dan Lihat Kartu</button>
            </form>
        </div>
    </section>
@endsection
