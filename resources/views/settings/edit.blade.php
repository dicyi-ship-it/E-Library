@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')

@section('content')
    <div class="mb-8">
        <h1 class="page-title">Pengaturan Aplikasi</h1>
        <p class="page-subtitle">Ubah identitas aplikasi untuk sekolah, kampus, atau institusi lain.</p>
    </div>

    <form class="panel grid max-w-3xl gap-4" method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        <label>
            <span class="mb-1 block text-sm font-semibold text-slate-700">Nama Aplikasi</span>
            <input class="input" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required>
        </label>

        <label>
            <span class="mb-1 block text-sm font-semibold text-slate-700">Nama Institusi</span>
            <input class="input" name="institution_name" value="{{ old('institution_name', $settings['institution_name']) }}" required>
        </label>

        <label>
            <span class="mb-1 block text-sm font-semibold text-slate-700">Nama Perpustakaan</span>
            <input class="input" name="library_name" value="{{ old('library_name', $settings['library_name']) }}" required>
        </label>

        <label>
            <span class="mb-1 block text-sm font-semibold text-slate-700">Teks Logo Singkat</span>
            <input class="input" name="logo_text" value="{{ old('logo_text', $settings['logo_text']) }}" maxlength="6" required>
        </label>

        <div class="rounded-md border border-sky-100 bg-sky-50 p-4">
            <p class="text-sm font-semibold text-sky-900">Preview</p>
            <div class="mt-3 flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-md bg-sky-700 text-sm font-bold text-white">{{ old('logo_text', $settings['logo_text']) }}</span>
                <div>
                    <p class="font-bold text-slate-950">{{ old('institution_name', $settings['institution_name']) }}</p>
                    <p class="text-sm text-slate-500">{{ old('library_name', $settings['library_name']) }} - {{ old('app_name', $settings['app_name']) }}</p>
                </div>
            </div>
        </div>

        <button class="btn-primary w-fit" type="submit">Simpan Pengaturan</button>
    </form>
@endsection
