@extends('layouts.app')

@section('title', $book->title.' - Katalog Buku')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a class="btn-secondary" href="{{ route('landing') }}#koleksi">Kembali ke Katalog</a>
        <a class="btn-primary" href="{{ route('attendance.kiosk') }}">Isi Daftar Hadir</a>
    </div>

    <section class="grid gap-8 lg:grid-cols-[340px_1fr]">
        <aside class="space-y-4">
            <div class="panel">
                @if($book->cover_path)
                    <img class="h-[440px] w-full rounded-md object-cover" src="{{ asset('storage/'.$book->cover_path) }}" alt="Cover {{ $book->title }}">
                @else
                    <div class="grid h-[440px] place-items-center rounded-md bg-slate-950 px-6 text-center text-3xl font-bold text-white">DDC {{ $book->ddc }}</div>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="metric"><span>{{ $book->stock_available }}</span><small>Stok tersedia</small></div>
                <div class="metric"><span>{{ $book->stock_total }}</span><small>Total koleksi</small></div>
            </div>
        </aside>

        <article class="space-y-6">
            <div class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <span class="badge">Buku Fisik</span>
                    <span class="badge badge-soft">{{ $book->category ?: 'Katalog Umum' }}</span>
                    <span class="badge badge-soft">{{ $book->status === 'available' ? 'Tersedia' : ucfirst($book->status) }}</span>
                </div>
                <div>
                    <h1 class="max-w-4xl text-4xl font-bold leading-tight text-slate-950">{{ $book->title }}</h1>
                    @if($book->subtitle)
                        <p class="mt-2 text-xl text-slate-600">{{ $book->subtitle }}</p>
                    @endif
                    <p class="mt-3 text-slate-500">{{ $book->author }} &middot; {{ $book->publisher ?: 'Penerbit tidak tersedia' }} &middot; {{ $book->publication_year ?: 'Tahun tidak tersedia' }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="info-box"><small>Nomor Panggil</small><strong>{{ $book->call_number ?: '-' }}</strong></div>
                <div class="info-box"><small>DDC</small><strong>{{ $book->ddc }}</strong></div>
                <div class="info-box"><small>Rak</small><strong>{{ $book->rack ?: '-' }}</strong></div>
                <div class="info-box"><small>Lokasi</small><strong>{{ $book->location ?: '-' }}</strong></div>
                <div class="info-box"><small>ISBN</small><strong>{{ $book->isbn ?: '-' }}</strong></div>
                <div class="info-box"><small>Bahasa</small><strong>{{ $book->language ?: '-' }}</strong></div>
                <div class="info-box"><small>Edisi</small><strong>{{ $book->edition ?: '-' }}</strong></div>
                <div class="info-box"><small>Kategori</small><strong>{{ $book->category ?: '-' }}</strong></div>
            </div>

            <div class="panel">
                <h2 class="section-title">Informasi Buku</h2>
                <p class="mt-3 leading-7 text-slate-600">{{ $book->description ?: 'Deskripsi buku belum tersedia. Gunakan informasi rak, DDC, dan nomor panggil untuk menemukan koleksi ini di perpustakaan.' }}</p>
            </div>

            @if($relatedBooks->isNotEmpty())
                <section>
                    <h2 class="section-title mb-4">Koleksi Terkait</h2>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach($relatedBooks as $related)
                            <a class="rounded-lg border border-slate-200 bg-white p-4 transition hover:border-sky-300 hover:shadow-md" href="{{ route('catalog.books.show', $related) }}">
                                <p class="line-clamp-2 font-semibold text-slate-950">{{ $related->title }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ $related->author }}</p>
                                <p class="mt-3 text-xs font-semibold text-sky-700">DDC {{ $related->ddc }} &middot; Rak {{ $related->rack ?: '-' }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </article>
    </section>
@endsection
