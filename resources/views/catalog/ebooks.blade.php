@extends('layouts.app')

@section('title', 'Katalog Ebook')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Katalog Ebook</h1>
            <p class="page-subtitle">Telusuri ebook aktif berdasarkan judul dan sumber koleksi.</p>
        </div>
        <a class="btn-secondary" href="{{ route('catalog.books.index') }}">Lihat Katalog Buku</a>
    </div>

    <form method="GET" action="{{ route('catalog.ebooks.index') }}" class="panel mb-6 grid gap-3 lg:grid-cols-[1fr_auto_auto]">
        <input class="input min-h-12" name="q" value="{{ $search }}" placeholder="Cari judul, penulis, atau kategori ebook">
        <select class="input min-h-12 lg:w-56" name="category">
            <option value="">Semua kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <button class="btn-primary min-h-12" type="submit">Cari Ebook</button>
    </form>

    <div class="grid gap-5 lg:grid-cols-2">
        @forelse($ebooks as $ebook)
            <article class="collection-card">
                <a href="{{ route('catalog.ebooks.show', $ebook) }}" class="grid gap-4 sm:grid-cols-[112px_1fr]">
                    @if($ebook->cover_path)
                        <img class="h-40 w-full rounded-md object-cover sm:h-36" src="{{ asset('storage/'.$ebook->cover_path) }}" alt="Cover {{ $ebook->title }}">
                    @else
                        <div class="grid h-40 place-items-center rounded-md bg-sky-700 px-3 text-center text-sm font-bold text-white sm:h-36">PDF</div>
                    @endif
                    <div class="min-w-0">
                        <h2 class="line-clamp-3 text-xl font-bold text-slate-950">{{ $ebook->title }}</h2>
                        <p class="mt-3 line-clamp-2 text-sm text-slate-600">Sumber: {{ $ebook->author ?: $appSettings['library_name'].' '.$appSettings['institution_name'] }}</p>
                    </div>
                </a>
            </article>
        @empty
            <div class="panel lg:col-span-2 text-slate-600">Belum ada ebook yang cocok. Coba kata kunci lain atau kosongkan filter.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $ebooks->links() }}</div>
@endsection
