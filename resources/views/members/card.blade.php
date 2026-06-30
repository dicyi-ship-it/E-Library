@extends('layouts.app')

@section('title', 'Kartu Anggota')

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1fr_.75fr]">
        <div>
            <p class="text-sm font-semibold text-sky-700">Kartu Anggota Perpustakaan</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-950">{{ $member->name }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                QR pada kartu ini berisi nomor induk anggota. Kartu dapat digunakan untuk proses absensi perpustakaan dan identifikasi layanan anggota.
            </p>
        </div>

        <div class="flex flex-wrap items-start justify-start gap-3 lg:justify-end">
            <a class="btn-secondary" href="{{ route('ebooks.reader') }}">Buka Ebook</a>
            <button class="btn-primary" type="button" onclick="window.print()">Cetak Kartu</button>
        </div>
    </section>

    <section class="mt-8 grid gap-6 lg:grid-cols-[.9fr_1.1fr]">
        <article class="overflow-hidden rounded-lg border border-sky-100 bg-white shadow-lg shadow-sky-100">
            <div class="bg-slate-950 p-6 text-white">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-sky-200">{{ $appSettings['institution_name'] }}</p>
                        <h2 class="mt-1 text-2xl font-bold">{{ $appSettings['library_name'] }}</h2>
                    </div>
                    <span class="grid h-12 w-12 place-items-center rounded-md bg-sky-600 text-base font-bold">{{ $appSettings['logo_text'] }}</span>
                </div>
            </div>

            <div class="grid gap-6 p-6 sm:grid-cols-[1fr_180px]">
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-500">Nama Anggota</p>
                    <h3 class="mt-1 text-2xl font-bold text-slate-950">{{ $member->name }}</h3>

                    <div class="mt-6 grid gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">Nomor Induk</p>
                            <p class="mt-1 text-lg font-bold text-slate-950">{{ $member->identity_number ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-slate-500">ID Anggota</p>
                            <p class="mt-1 text-lg font-bold text-slate-950">{{ $member->member_id ?: '-' }}</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold uppercase text-slate-500">Jenis</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $member->level ?: 'Member' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase text-slate-500">Status</p>
                                <p class="mt-1 font-semibold text-sky-700">{{ ucfirst($member->status ?: 'active') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-center">
                    <img class="mx-auto h-40 w-40 rounded-md bg-white p-2" src="{{ $qrCode }}" alt="QR nomor induk {{ $member->identity_number ?: $member->member_id }}">
                    <p class="mt-3 break-words text-xs font-semibold text-slate-600">{{ $qrPayload }}</p>
                </div>
            </div>
        </article>

        <aside class="grid content-start gap-4">
            <div class="info-box">
                <small>Email</small>
                <strong>{{ $member->email }}</strong>
            </div>
            <div class="info-box">
                <small>Fakultas / Unit</small>
                <strong>{{ $member->faculty ?: '-' }}</strong>
            </div>
            <div class="info-box">
                <small>Program Studi / Bagian</small>
                <strong>{{ $member->study_program ?: $member->department ?: '-' }}</strong>
            </div>
            <div class="info-box">
                <small>Terdaftar</small>
                <strong>{{ $member->registered_at?->format('d M Y') ?: '-' }}</strong>
            </div>
        </aside>
    </section>
@endsection
