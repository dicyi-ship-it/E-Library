@extends('layouts.app')

@section('title', 'Ebook Member')

@section('content')
    <div class="mb-8">
        <h1 class="page-title">Koleksi Ebook</h1>
        <p class="page-subtitle">Halo, {{ auth()->user()->name }}. ID anggota: {{ auth()->user()->member_id ?: '-' }}</p>
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
                    <p class="mt-1 text-sm text-zinc-500">{{ $ebook->author ?: 'Perpustakaan' }} · {{ $ebook->category ?: 'Digital' }}</p>
                    <p class="mt-3 line-clamp-3 text-sm text-zinc-600">{{ $ebook->description }}</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a class="btn-mini" href="{{ route('ebooks.read', $ebook) }}">Baca</a>
                    <a class="btn-mini" href="{{ route('ebooks.download', $ebook) }}">Unduh</a>
                </div>
            </article>
        @empty
            <div class="panel sm:col-span-2 lg:col-span-4">Belum ada ebook aktif.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $ebooks->links() }}</div>
@endsection
