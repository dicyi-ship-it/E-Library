@extends('layouts.app')

@section('title', $appSettings['library_name'].' '.$appSettings['institution_name'])

@section('content')
    <section class="overflow-hidden rounded-lg border border-sky-100 bg-gradient-to-br from-sky-50 via-white to-slate-100">
        <div class="grid gap-10 px-6 py-12 lg:grid-cols-[1.15fr_.85fr] lg:px-10 lg:py-16">
            <div class="space-y-7">
                <span class="inline-flex w-fit rounded-md border border-sky-200 bg-white px-3 py-1 text-sm font-semibold text-sky-800">{{ $appSettings['library_name'] }} {{ $appSettings['institution_name'] }}</span>
                <div>
                    <h1 class="max-w-4xl text-4xl font-bold leading-tight text-slate-950 sm:text-5xl">Katalog perpustakaan yang rapi untuk buku fisik dan ebook.</h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-slate-600">
                        Telusuri koleksi buku, cek lokasi rak dan stok, akses ebook digital, serta gunakan daftar hadir perpustakaan dalam satu aplikasi open source.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a class="btn-primary" href="{{ route('catalog.books.index') }}">Katalog Buku</a>
                    <a class="btn-secondary" href="{{ route('catalog.ebooks.index') }}">Katalog Ebook</a>
                    <a class="btn-secondary" href="{{ route('attendance.kiosk') }}">Daftar Hadir</a>
                </div>
            </div>

            <div class="grid content-center gap-4">
                <form class="rounded-lg border border-sky-100 bg-white p-4 shadow-sm" method="GET" action="{{ route('catalog.books.index') }}">
                    <p class="text-sm font-semibold text-sky-700">Cari Buku Fisik</p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_auto]">
                        <input class="input min-h-12" name="q" placeholder="Judul, penulis, ISBN, DDC">
                        <button class="btn-primary min-h-12" type="submit">Cari</button>
                    </div>
                </form>

                <form class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm" method="GET" action="{{ route('catalog.ebooks.index') }}">
                    <p class="text-sm font-semibold text-slate-700">Cari Ebook</p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-[1fr_auto]">
                        <input class="input min-h-12" name="q" placeholder="Judul, penulis, kategori">
                        <button class="btn-secondary min-h-12" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="py-10">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="metric"><span>{{ $bookCount }}</span><small>Buku fisik</small></div>
            <div class="metric"><span>{{ $ebookCount }}</span><small>Ebook aktif</small></div>
            <div class="metric"><span>{{ $visitCount }}</span><small>Hadir hari ini</small></div>
            <div class="metric"><span>{{ $loanCount }}</span><small>Sedang dipinjam</small></div>
        </div>
    </section>

    <section id="koleksi-buku" class="py-10">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-sky-700">Katalog Buku</p>
                <h2 class="page-title">Buku Fisik Terbaru</h2>
                <p class="page-subtitle">Koleksi buku fisik dipisahkan dari ebook agar lokasi rak, DDC, dan stok lebih mudah dibaca.</p>
            </div>
            <a class="btn-secondary" href="{{ route('catalog.books.index') }}">Lihat Semua Buku</a>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($books as $book)
                <a href="{{ route('catalog.books.show', $book) }}" class="collection-card block">
                    <div class="flex gap-4">
                        <div class="h-36 w-24 shrink-0 overflow-hidden rounded-md bg-slate-100">
                            @if($book->cover_path)
                                <img class="h-full w-full object-cover" src="{{ asset('storage/'.$book->cover_path) }}" alt="Cover {{ $book->title }}">
                            @else
                                <div class="grid h-full w-full place-items-center bg-slate-900 px-2 text-center text-xs font-bold text-white">DDC {{ $book->ddc }}</div>
                            @endif
                        </div>
                        <div class="min-w-0 py-1">
                            <span class="badge">Buku Fisik</span>
                            <h3 class="mt-3 line-clamp-2 text-lg font-bold text-slate-950">{{ $book->title }}</h3>
                            <p class="mt-2 line-clamp-1 text-sm text-slate-500">{{ $book->author }}</p>
                            <div class="mt-4 grid gap-1 text-sm text-slate-600">
                                <span>Rak <strong class="text-slate-900">{{ $book->rack ?: '-' }}</strong></span>
                                <span>DDC <strong class="text-slate-900">{{ $book->ddc }}</strong></span>
                                <span>Stok <strong class="text-slate-900">{{ $book->stock_available }}/{{ $book->stock_total }}</strong></span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="panel md:col-span-2 xl:col-span-3 text-slate-600">Belum ada buku fisik.</div>
            @endforelse
        </div>
    </section>

    <section id="koleksi-ebook" class="border-y border-sky-100 bg-white py-10">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-sky-700">Katalog Ebook</p>
                <h2 class="page-title">Ebook Digital Terbaru</h2>
                <p class="page-subtitle">Koleksi digital ditampilkan dalam section terpisah agar akses file PDF dan metadata ebook lebih jelas.</p>
            </div>
            <a class="btn-secondary" href="{{ route('catalog.ebooks.index') }}">Lihat Semua Ebook</a>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($ebooks as $ebook)
                <a href="{{ route('catalog.ebooks.show', $ebook) }}" class="collection-card block">
                    <div class="flex gap-4">
                        <div class="h-36 w-24 shrink-0 overflow-hidden rounded-md bg-sky-100">
                            @if($ebook->cover_path)
                                <img class="h-full w-full object-cover" src="{{ asset('storage/'.$ebook->cover_path) }}" alt="Cover {{ $ebook->title }}">
                            @else
                                <div class="grid h-full w-full place-items-center bg-sky-700 px-2 text-center text-sm font-bold text-white">PDF</div>
                            @endif
                        </div>
                        <div class="min-w-0 py-1">
                            <span class="badge">Ebook PDF</span>
                            <h3 class="mt-3 line-clamp-2 text-lg font-bold text-slate-950">{{ $ebook->title }}</h3>
                            <p class="mt-2 line-clamp-1 text-sm text-slate-500">{{ $ebook->author ?: $appSettings['library_name'].' '.$appSettings['institution_name'] }}</p>
                            <div class="mt-4 grid gap-1 text-sm text-slate-600">
                                <span>Kategori <strong class="text-slate-900">{{ $ebook->category ?: 'Digital' }}</strong></span>
                                <span>Diunduh <strong class="text-slate-900">{{ $ebook->download_count }} kali</strong></span>
                                <span>Status <strong class="text-slate-900">Aktif</strong></span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="panel md:col-span-2 xl:col-span-3 text-slate-600">Belum ada ebook aktif.</div>
            @endforelse
        </div>
    </section>

    <section id="akses" class="grid gap-6 py-10 lg:grid-cols-[.9fr_1.1fr]">
        <div class="rounded-lg bg-slate-950 p-6 text-white">
            <p class="text-sm font-semibold text-sky-200">Layanan Cepat</p>
            <h2 class="mt-3 text-3xl font-bold">Absensi dan katalog dibuat untuk rutinitas perpustakaan.</h2>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <a class="rounded-md border border-white/15 bg-white/10 p-4 transition hover:bg-white/15" href="{{ route('attendance.kiosk') }}">
                    <span class="text-sm text-sky-100">Daftar hadir</span>
                    <strong class="mt-1 block text-lg">Scan QR atau input nomor induk</strong>
                </a>
                <a class="rounded-md border border-white/15 bg-white/10 p-4 transition hover:bg-white/15" href="{{ route('catalog.books.index') }}">
                    <span class="text-sm text-sky-100">Katalog buku</span>
                    <strong class="mt-1 block text-lg">Cek rak, DDC, dan stok buku</strong>
                </a>
                <a class="rounded-md border border-white/15 bg-white/10 p-4 transition hover:bg-white/15" href="{{ route('catalog.ebooks.index') }}">
                    <span class="text-sm text-sky-100">Katalog ebook</span>
                    <strong class="mt-1 block text-lg">Akses koleksi digital aktif</strong>
                </a>
            </div>
        </div>

        @guest
            <div class="grid gap-4">
                <form method="POST" action="{{ route('login') }}" class="panel grid gap-4">
                    @csrf
                    <h2 class="section-title">Masuk Anggota</h2>
                    <input class="input" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    <input class="input" type="password" name="password" placeholder="Password" required>
                    <button class="btn-primary" type="submit">Masuk</button>
                </form>

                <form method="POST" action="{{ route('register') }}" class="panel grid gap-3">
                    @csrf
                    <h2 class="section-title">Registrasi Member</h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <input class="input" name="name" placeholder="Nama lengkap" value="{{ old('name') }}" required>
                        <input class="input" type="email" name="email" placeholder="Email kampus" value="{{ old('email') }}" required>
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
                    </div>
                    <button class="btn-primary w-fit" type="submit">Daftar dan Akses Ebook</button>
                </form>
            </div>
        @else
            <div class="panel grid content-center gap-4">
                <h2 class="section-title">Halo, {{ auth()->user()->name }}</h2>
                <p class="text-sm leading-6 text-slate-600">Akun Anda aktif. Lanjutkan ke dashboard atau buka koleksi ebook anggota.</p>
                <div class="flex flex-wrap gap-3">
                    <a class="btn-primary" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('ebooks.reader') }}">Buka Aplikasi</a>
                    <a class="btn-secondary" href="{{ route('ebooks.reader') }}">Koleksi Ebook</a>
                </div>
            </div>
        @endguest
    </section>
@endsection
