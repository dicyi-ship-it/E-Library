@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Katalog Buku</h1>
            <p class="page-subtitle">Telusuri koleksi buku fisik, lokasi rak, DDC, dan ketersediaan stok.</p>
        </div>
        <a class="btn-secondary" href="{{ route('catalog.ebooks.index') }}">Lihat Katalog Ebook</a>
    </div>

    <form method="GET" action="{{ route('catalog.books.index') }}" class="panel mb-6 grid gap-3 lg:grid-cols-[1fr_auto_auto]">
        <input class="input min-h-12" name="q" value="{{ $search }}" placeholder="Cari judul, penulis, penerbit, ISBN, DDC, atau kategori">
        <select class="input min-h-12 lg:w-56" name="category">
            <option value="">Semua kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <button class="btn-primary min-h-12" type="submit">Cari Buku</button>
    </form>

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
                        <h2 class="mt-3 line-clamp-2 text-xl font-bold text-slate-950">{{ $book->title }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $book->author }} &middot; {{ $book->publisher ?: 'Penerbit tidak tersedia' }} &middot; {{ $book->publication_year ?: '-' }}</p>
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
            <div class="panel lg:col-span-2 text-slate-600">Belum ada buku yang cocok. Coba kata kunci lain atau kosongkan filter.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $books->links() }}</div>
@endsection
