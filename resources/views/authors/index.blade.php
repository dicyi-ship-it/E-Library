@extends('layouts.app')

@section('title', 'Penulis')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Indeks Penulis</h1>
            <p class="page-subtitle">Pilih penulis untuk melihat koleksi buku yang terkait.</p>
        </div>
        <a class="btn-primary" href="{{ route('books.create') }}">Tambah Buku</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[360px_1fr]">
        <section class="panel">
            <form class="mb-4 flex gap-3" method="GET" action="{{ route('authors.index') }}">
                <input class="input" name="search" placeholder="Cari nama penulis" value="{{ request('search') }}">
                <button class="btn-secondary" type="submit">Cari</button>
            </form>

            <div class="space-y-2">
                @forelse($authors as $author)
                    <a class="flex items-center justify-between rounded-md border border-slate-200 px-4 py-3 transition hover:border-sky-300 hover:bg-sky-50 {{ $selectedAuthor?->is($author) ? 'border-sky-500 bg-sky-50' : 'bg-white' }}" href="{{ route('authors.show', $author) }}">
                        <span class="font-semibold text-slate-950">{{ $author->name }}</span>
                        <span class="badge">{{ $author->books_count }} buku</span>
                    </a>
                @empty
                    <p class="rounded-md border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada penulis terindeks.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $authors->links() }}</div>
        </section>

        <section class="panel">
            @if($selectedAuthor)
                <div class="mb-5">
                    <p class="text-sm font-semibold text-sky-700">Koleksi Penulis</p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-950">{{ $selectedAuthor->name }}</h2>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($books as $book)
                        <a class="block rounded-lg border border-slate-200 bg-white p-4 transition hover:border-sky-300 hover:shadow-md" href="{{ route('catalog.books.show', $book) }}">
                            <div class="flex gap-4">
                                @if($book->cover_path)
                                    <img class="h-24 w-16 rounded object-cover" src="{{ asset('storage/'.$book->cover_path) }}" alt="Cover {{ $book->title }}">
                                @else
                                    <div class="grid h-24 w-16 place-items-center rounded bg-slate-200 text-xs font-bold text-slate-600">DDC</div>
                                @endif
                                <div class="min-w-0">
                                    <h3 class="line-clamp-2 font-bold text-slate-950">{{ $book->title }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $book->publisher ?: 'Penerbit tidak tersedia' }} &middot; {{ $book->publication_year ?: '-' }}</p>
                                    <p class="mt-2 text-xs font-semibold text-sky-700">DDC {{ $book->ddc }} &middot; Rak {{ $book->rack ?: '-' }}</p>
                                    <p class="mt-3 text-xs font-semibold text-slate-500">Klik untuk lihat informasi buku</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="rounded-md border border-dashed border-slate-300 p-4 text-sm text-slate-500">Belum ada buku untuk penulis ini.</p>
                    @endforelse
                </div>

                <div class="mt-4">{{ $books->links() }}</div>
            @else
                <div class="grid min-h-72 place-items-center rounded-lg border border-dashed border-slate-300 p-6 text-center">
                    <div>
                        <h2 class="text-xl font-bold text-slate-950">Pilih penulis</h2>
                        <p class="mt-2 max-w-md text-sm leading-6 text-slate-500">Klik salah satu nama di kiri untuk melihat semua koleksi buku yang ditulis oleh penulis tersebut.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>
@endsection
