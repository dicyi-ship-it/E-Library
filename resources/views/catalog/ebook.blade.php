@extends('layouts.app')

@section('title', $ebook->title.' - Katalog Ebook')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <a class="btn-secondary" href="{{ route('landing') }}#koleksi">Kembali ke Katalog</a>
        <a class="btn-primary" href="{{ route('attendance.kiosk') }}">Isi Daftar Hadir</a>
    </div>

    <section class="grid gap-8 lg:grid-cols-[340px_1fr]">
        <aside class="space-y-4">
            <div class="panel">
                @if($ebook->cover_path)
                    <img class="h-[440px] w-full rounded-md object-cover" src="{{ asset('storage/'.$ebook->cover_path) }}" alt="Cover {{ $ebook->title }}">
                @else
                    <div class="grid h-[440px] place-items-center rounded-md bg-sky-700 px-6 text-center text-4xl font-bold text-white">PDF</div>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="metric"><span>{{ $ebook->download_count }}</span><small>Total unduhan</small></div>
                <div class="metric"><span>PDF</span><small>Format digital</small></div>
            </div>
        </aside>

        <article class="space-y-6">
            <div class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <span class="badge">Ebook PDF</span>
                    <span class="badge badge-soft">{{ $ebook->category ?: 'Digital' }}</span>
                    <span class="badge badge-soft">Aktif</span>
                </div>
                <div>
                    <h1 class="max-w-4xl text-4xl font-bold leading-tight text-slate-950">{{ $ebook->title }}</h1>
                    <p class="mt-3 text-slate-500">{{ $ebook->author ?: $appSettings['library_name'].' '.$appSettings['institution_name'] }}</p>
                </div>
            </div>

            <div class="panel">
                <h2 class="section-title">Ringkasan Ebook</h2>
                <p class="mt-3 leading-7 text-slate-600">{{ $ebook->description ?: 'Ebook aktif dan dapat diakses melalui akun anggota perpustakaan.' }}</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    @auth
                        <a class="btn-primary" href="{{ route('ebooks.read', $ebook) }}">Baca Ebook</a>
                        <a class="btn-secondary" href="{{ route('ebooks.download', $ebook) }}">Unduh PDF</a>
                    @else
                        <a class="btn-primary" href="{{ route('landing') }}#akses">Masuk untuk Baca</a>
                        <a class="btn-secondary" href="{{ route('landing') }}#akses">Daftar Anggota</a>
                    @endauth
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="info-box"><small>Kategori</small><strong>{{ $ebook->category ?: '-' }}</strong></div>
                <div class="info-box"><small>Status</small><strong>{{ $ebook->is_active ? 'Aktif' : 'Nonaktif' }}</strong></div>
                <div class="info-box"><small>Sumber</small><strong>{{ $ebook->file_path ? 'File internal' : 'Tautan eksternal' }}</strong></div>
            </div>

            @if($relatedEbooks->isNotEmpty())
                <section>
                    <h2 class="section-title mb-4">Ebook Terkait</h2>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach($relatedEbooks as $related)
                            <a class="rounded-lg border border-slate-200 bg-white p-4 transition hover:border-sky-300 hover:shadow-md" href="{{ route('catalog.ebooks.show', $related) }}">
                                <p class="line-clamp-2 font-semibold text-slate-950">{{ $related->title }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ $related->author ?: 'Perpustakaan' }}</p>
                                <p class="mt-3 text-xs font-semibold text-sky-700">{{ $related->category ?: 'Digital' }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </article>
    </section>
@endsection
