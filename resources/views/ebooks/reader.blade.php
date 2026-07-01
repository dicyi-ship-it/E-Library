@extends('layouts.app')

@section('title', 'Ebook Saya')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Ebook Saya</h1>
            <p class="page-subtitle">Daftar ebook yang sudah Anda baca atau unduh dari katalog ebook.</p>
        </div>
        <a class="btn-secondary" href="{{ route('catalog.ebooks.index') }}">Cari Ebook</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($ebooks as $ebook)
            <article class="flex min-h-72 flex-col justify-between rounded-lg border border-zinc-200 bg-white p-4 shadow-sm">
                <div>
                    @if($ebook->cover_path)
                        <img class="mb-4 h-40 w-full rounded-md object-cover" src="{{ asset('storage/'.$ebook->cover_path) }}" alt="Cover {{ $ebook->title }}">
                    @else
                        <div class="mb-4 grid h-40 place-items-center rounded-md bg-zinc-900 text-lg font-semibold text-white">EBOOK</div>
                    @endif
                    <p class="font-semibold">{{ $ebook->title }}</p>
                    <p class="mt-1 text-sm text-zinc-500">{{ $ebook->author ?: 'Perpustakaan' }} &middot; {{ $ebook->category ?: 'Digital' }}</p>
                    <p class="mt-3 line-clamp-3 text-sm text-zinc-600">{{ $ebook->description }}</p>

                    <div class="mt-4 rounded-md bg-slate-50 p-3 text-xs text-slate-600">
                        <p>Dibaca: <strong>{{ $ebook->pivot->read_count }}</strong> kali</p>
                        <p>Diunduh: <strong>{{ $ebook->pivot->download_count }}</strong> kali</p>
                        <p>Terakhir akses: <strong>{{ $ebook->pivot->updated_at?->format('d M Y H:i') }}</strong></p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a class="btn-mini" href="{{ route('ebooks.read', $ebook) }}">Baca</a>
                    <a class="btn-mini" href="{{ route('ebooks.download', $ebook) }}">Unduh</a>
                    <form method="POST" action="{{ route('ebooks.reader.remove', $ebook) }}" onsubmit="return confirm('Hapus ebook ini dari daftar Anda?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn-mini-danger" type="submit">Hapus dari Daftar</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="panel sm:col-span-2 lg:col-span-4">
                <h2 class="section-title">Belum ada ebook di daftar Anda.</h2>
                <p class="mt-2 text-sm text-slate-600">Buka katalog ebook, lalu pilih baca atau unduh untuk menambahkan ebook ke halaman ini.</p>
                <a class="btn-primary mt-4" href="{{ route('catalog.ebooks.index') }}">Buka Katalog Ebook</a>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $ebooks->links() }}</div>
@endsection
