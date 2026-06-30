<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $appSettings['app_name'])</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div class="min-h-screen">
        <header class="sticky top-0 z-40 border-b border-sky-100 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 font-semibold">
                    <span class="grid h-10 w-10 place-items-center rounded-md bg-sky-700 text-sm font-bold text-white">{{ $appSettings['logo_text'] }}</span>
                    <span>{{ $appSettings['institution_name'] }} Library</span>
                </a>

                <nav class="flex flex-wrap items-center gap-2 text-sm">
                    <a class="nav-link" href="{{ route('landing') }}#koleksi">Katalog</a>
                    <a class="nav-link" href="{{ route('attendance.kiosk') }}">Daftar Hadir</a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            <a class="nav-link" href="{{ route('books.index') }}">Buku</a>
                            <a class="nav-link" href="{{ route('authors.index') }}">Penulis</a>
                            <a class="nav-link" href="{{ route('publishers.index') }}">Penerbit</a>
                            <a class="nav-link" href="{{ route('members.index') }}">Anggota</a>
                            <a class="nav-link" href="{{ route('attendance.index') }}">Kehadiran</a>
                            <a class="nav-link" href="{{ route('ebooks.index') }}">Ebook</a>
                            <a class="nav-link" href="{{ route('circulation.index') }}">Sirkulasi</a>
                            <a class="nav-link" href="{{ route('settings.edit') }}">Pengaturan</a>
                        @else
                            <a class="nav-link" href="{{ route('ebooks.reader') }}">Ebook Saya</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn-secondary" type="submit">Keluar</button>
                        </form>
                    @else
                        <a class="btn-secondary" href="{{ route('landing') }}#akses">Masuk</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="mb-6 rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                    <strong>Periksa kembali data:</strong>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
