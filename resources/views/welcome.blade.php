@extends('layouts.app')

@section('title', $appSettings['library_name'].' '.$appSettings['institution_name'])

@section('content')
    <section class="grid min-h-[76vh] items-center gap-10 py-4 lg:grid-cols-[1.05fr_.95fr]">
        <div class="space-y-8">
            <div class="space-y-5">
                <span class="inline-flex w-fit rounded-md border border-sky-200 bg-sky-50 px-3 py-1 text-sm font-semibold text-sky-800">{{ $appSettings['library_name'] }} {{ $appSettings['institution_name'] }}</span>
                <h1 class="max-w-3xl text-4xl font-bold leading-tight text-slate-950 sm:text-5xl">Katalog buku dan ebook kampus dalam satu ruang baca digital.</h1>
                <p class="max-w-2xl text-lg leading-8 text-slate-600">
                    Temukan buku fisik, lihat ketersediaan rak, akses ebook PDF, dan catat kehadiran perpustakaan lewat QR atau nomor induk institusi.
                </p>
            </div>

            <form method="GET" action="{{ route('landing') }}" class="rounded-lg border border-sky-100 bg-white p-3 shadow-sm shadow-sky-100">
                <div class="grid gap-3 lg:grid-cols-[1fr_auto_auto]">
                    <input class="input min-h-12" name="q" value="{{ $search }}" placeholder="Cari judul, pengarang, ISBN, DDC, atau kategori">
                    <select class="input min-h-12 lg:w-44" name="format">
                        <option value="all" @selected($selectedFormat === 'all')>Semua koleksi</option>
                        <option value="book" @selected($selectedFormat === 'book')>Buku fisik</option>
                        <option value="ebook" @selected($selectedFormat === 'ebook')>Ebook PDF</option>
                    </select>
                    <button class="btn-primary min-h-12" type="submit">Cari Koleksi</button>
                </div>
                <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                    <a class="filter-chip {{ ! $selectedCategory ? 'filter-chip-active' : '' }}" href="{{ route('landing', request()->except('category')) }}">Semua</a>
                    @foreach($categories->take(8) as $item)
                        <a class="filter-chip {{ $selectedCategory === $item ? 'filter-chip-active' : '' }}" href="{{ route('landing', array_merge(request()->except('page'), ['category' => $item])) }}">{{ $item }}</a>
                    @endforeach
                </div>
            </form>

            <div class="grid gap-3 sm:grid-cols-4">
                <div class="metric"><span>{{ $bookCount }}</span><small>Buku</small></div>
                <div class="metric"><span>{{ $ebookCount }}</span><small>Ebook aktif</small></div>
                <div class="metric"><span>{{ $visitCount }}</span><small>Hadir hari ini</small></div>
                <div class="metric"><span>{{ $loanCount }}</span><small>Sedang dipinjam</small></div>
            </div>

            <div class="flex flex-wrap gap-3">
                <a class="btn-primary" href="#koleksi">Lihat Katalog</a>
                <a class="btn-secondary" href="{{ route('attendance.kiosk') }}">Isi Daftar Hadir</a>
                @auth
                    <a class="btn-secondary" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('ebooks.reader') }}">Buka Akun</a>
                @else
                    <a class="btn-secondary" href="#akses">Masuk / Daftar</a>
                @endauth
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-4 rounded-lg bg-sky-100/60"></div>
            <div class="relative overflow-hidden rounded-lg border border-sky-100 bg-white shadow-lg shadow-sky-100">
                <div class="border-b border-slate-100 bg-slate-950 px-5 py-4 text-white">
                    <p class="text-sm font-semibold text-sky-200">OPAC Preview</p>
                    <p class="mt-1 text-2xl font-bold">Koleksi Rekomendasi</p>
                </div>
                <div class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-3">
                    @forelse($books->take(6) as $book)
                        <a href="{{ route('catalog.books.show', $book) }}" class="group flex min-h-48 flex-col justify-between rounded-md border border-slate-200 bg-slate-50 p-3 transition hover:-translate-y-1 hover:border-sky-300 hover:bg-white hover:shadow-md">
                            <div>
                                @if($book->cover_path)
                                    <img class="mb-3 h-24 w-full rounded-md object-cover" src="{{ asset('storage/'.$book->cover_path) }}" alt="Cover {{ $book->title }}">
                                @else
                                    <div class="mb-3 grid h-24 place-items-center rounded-md bg-sky-700 text-xs font-bold text-white">DDC {{ $book->ddc }}</div>
                                @endif
                                <p class="line-clamp-2 text-sm font-semibold text-slate-950 group-hover:text-sky-700">{{ $book->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $book->author }}</p>
                            </div>
                            <p class="mt-3 text-xs font-medium text-sky-700">{{ $book->stock_available }}/{{ $book->stock_total }} tersedia</p>
                        </a>
                    @empty
                        <div class="col-span-2 rounded-md border border-dashed border-slate-300 p-6 text-sm text-slate-500 sm:col-span-3">Belum ada buku yang cocok dengan pencarian.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section id="koleksi" class="py-10">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="page-title">Katalog Terbaru</h2>
                <p class="page-subtitle">Klik koleksi untuk membuka informasi lengkap, lokasi rak, status stok, dan akses ebook.</p>
            </div>
            <a class="btn-secondary" href="{{ route('attendance.kiosk') }}">Check-in Perpustakaan</a>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            @forelse($books as $book)
                <article class="collection-card">
                    <a href="{{ route('catalog.books.show', $book) }}" class="grid gap-4 sm:grid-cols-[112px_1fr]">
                        @if($book->cover_path)
                            <img class="h-40 w-full rounded-md object-cover sm:h-36" src="{{ asset('storage/'.$book->cover_path) }}" alt="Cover {{ $book->title }}">
                        @else
                            <div class="grid h-40 place-items-center rounded-md bg-slate-900 px-3 text-center text-sm font-bold text-white sm:h-36">DDC {{ $book->ddc }}</div>
                        @endif
                        <div class="min-w-0">
                            <div class="flex flex-wrap gap-2">
                                <span class="badge">Buku Fisik</span>
                                <span class="badge badge-soft">{{ $book->category ?: 'Umum' }}</span>
                            </div>
                            <h3 class="mt-3 line-clamp-2 text-xl font-bold text-slate-950">{{ $book->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $book->author }} &middot; {{ $book->publication_year ?: 'Tahun tidak tersedia' }}</p>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $book->description ?: 'Informasi deskripsi belum tersedia.' }}</p>
                            <div class="mt-4 grid gap-2 text-sm text-slate-600 sm:grid-cols-3">
                                <span>Rak: <strong class="text-slate-900">{{ $book->rack ?: '-' }}</strong></span>
                                <span>DDC: <strong class="text-slate-900">{{ $book->ddc }}</strong></span>
                                <span>Stok: <strong class="text-slate-900">{{ $book->stock_available }}/{{ $book->stock_total }}</strong></span>
                            </div>
                        </div>
                    </a>
                </article>
            @empty
            @endforelse

            @forelse($ebooks as $ebook)
                <article class="collection-card">
                    <a href="{{ route('catalog.ebooks.show', $ebook) }}" class="grid gap-4 sm:grid-cols-[112px_1fr]">
                        @if($ebook->cover_path)
                            <img class="h-40 w-full rounded-md object-cover sm:h-36" src="{{ asset('storage/'.$ebook->cover_path) }}" alt="Cover {{ $ebook->title }}">
                        @else
                            <div class="grid h-40 place-items-center rounded-md bg-sky-700 px-3 text-center text-sm font-bold text-white sm:h-36">PDF</div>
                        @endif
                        <div class="min-w-0">
                            <div class="flex flex-wrap gap-2">
                                <span class="badge">Ebook PDF</span>
                                <span class="badge badge-soft">{{ $ebook->category ?: 'Digital' }}</span>
                            </div>
                            <h3 class="mt-3 line-clamp-2 text-xl font-bold text-slate-950">{{ $ebook->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $ebook->author ?: $appSettings['library_name'].' '.$appSettings['institution_name'] }}</p>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $ebook->description ?: 'Ebook aktif dan siap diakses oleh anggota.' }}</p>
                            <div class="mt-4 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                                <span>Format: <strong class="text-slate-900">PDF / Digital</strong></span>
                                <span>Diunduh: <strong class="text-slate-900">{{ $ebook->download_count }} kali</strong></span>
                            </div>
                        </div>
                    </a>
                </article>
            @empty
            @endforelse
        </div>

        @if($books->isEmpty() && $ebooks->isEmpty())
            <div class="panel mt-5 text-slate-600">Belum ada koleksi yang cocok. Coba kata kunci lain atau kosongkan filter.</div>
        @endif
    </section>

    <section id="akses" class="grid gap-6 py-10 lg:grid-cols-[.9fr_1.1fr]">
        <div class="rounded-lg bg-slate-950 p-6 text-white">
            <p class="text-sm font-semibold text-sky-200">Layanan Cepat</p>
            <h2 class="mt-3 text-3xl font-bold">Absensi, katalog, dan ebook dibuat untuk rutinitas perpustakaan kampus.</h2>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <a class="rounded-md border border-white/15 bg-white/10 p-4 transition hover:bg-white/15" href="{{ route('attendance.kiosk') }}">
                    <span class="text-sm text-sky-100">Daftar hadir</span>
                    <strong class="mt-1 block text-lg">Scan QR atau input nomor induk</strong>
                </a>
                <a class="rounded-md border border-white/15 bg-white/10 p-4 transition hover:bg-white/15" href="#koleksi">
                    <span class="text-sm text-sky-100">Katalog</span>
                    <strong class="mt-1 block text-lg">Cek rak, DDC, dan stok buku</strong>
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
