@extends('layouts.app')

@section('title', $ebook->title)

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">{{ $ebook->title }}</h1>
            <p class="page-subtitle">{{ $ebook->author ?: 'Perpustakaan' }} · {{ $ebook->category ?: 'Digital' }}</p>
        </div>
        <a class="btn-primary" href="{{ route('ebooks.download', $ebook) }}">Unduh</a>
    </div>

    <section class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
        @if($ebook->file_path && str_ends_with(strtolower($ebook->file_path), '.pdf'))
            <iframe class="h-[78vh] w-full" src="{{ asset('storage/'.$ebook->file_path) }}"></iframe>
        @elseif($ebook->external_url)
            <div class="p-6">
                <a class="btn-primary" href="{{ $ebook->external_url }}" target="_blank" rel="noopener">Buka Ebook</a>
            </div>
        @else
            <div class="p-6 text-zinc-600">File baca belum tersedia.</div>
        @endif
    </section>
@endsection
