@extends('layouts.app')

@section('title', 'Daftar Hadir Perpustakaan')

@section('content')
    <section class="grid min-h-[76vh] items-center gap-8 lg:grid-cols-[.95fr_1.05fr]">
        <div class="space-y-6">
            <span class="inline-flex w-fit rounded-md border border-sky-200 bg-sky-50 px-3 py-1 text-sm font-semibold text-sky-800">Daftar Hadir Perpustakaan</span>
            <div>
                <h1 class="text-4xl font-bold leading-tight text-slate-950 sm:text-5xl">Scan QR atau masukkan nomor induk untuk check-in.</h1>
                <p class="mt-4 max-w-2xl text-lg leading-8 text-slate-600">
                    Gunakan NIM, NIS, NIDN, NUPTK, nomor anggota, atau nomor induk unik lain yang berlaku di {{ $appSettings['institution_name'] }}.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="metric"><span>{{ $todayVisits }}</span><small>Kunjungan hari ini</small></div>
                <div class="metric"><span>{{ $activeVisitors }}</span><small>Pengunjung aktif</small></div>
            </div>

            <a class="btn-secondary w-fit" href="{{ route('landing') }}">Kembali ke Beranda</a>
        </div>

        <div class="panel" data-qr-attendance>
            <form class="grid gap-4" method="POST" action="{{ route('attendance.public.store') }}">
                @csrf
                <input type="hidden" name="attendance_source" value="manual" data-qr-source>

                <div class="rounded-lg border border-slate-200 bg-slate-950 p-4 text-white">
                    <div class="aspect-video overflow-hidden rounded-md bg-slate-900">
                        <video class="hidden h-full w-full object-cover" playsinline muted data-qr-video></video>
                        <div class="grid h-full place-items-center p-6 text-center" data-qr-placeholder>
                            <div>
                                <p class="text-sm font-semibold text-sky-200">QR Scanner</p>
                                <p class="mt-2 text-sm leading-6 text-slate-300">Aktifkan kamera, arahkan ke QR yang berisi nomor induk, lalu cek data sebelum kirim.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button class="btn-light" type="button" data-qr-start>Mulai Scan</button>
                        <button class="btn-dark" type="button" data-qr-stop>Berhenti</button>
                    </div>
                    <p class="mt-3 text-sm text-slate-300" data-qr-status>Scanner siap digunakan pada browser yang mendukung kamera dan QR detector.</p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="sm:col-span-2">
                        <span class="mb-1 block text-sm font-semibold text-slate-700">Nomor Induk</span>
                        <input class="input min-h-12 text-base" name="identity_number" data-qr-identity placeholder="NIM / NIS / NIDN / NUPTK / Nomor anggota" value="{{ old('identity_number') }}" required autofocus>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-700">Nama</span>
                        <input class="input" name="visitor_name" placeholder="Opsional jika sudah terdaftar" value="{{ old('visitor_name') }}">
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-700">Jenis Pengunjung</span>
                        <select class="input" name="visitor_type">
                            <option value="">Otomatis / Pengunjung</option>
                            @foreach(['Mahasiswa', 'Dosen', 'Tenaga Kependidikan', 'Siswa', 'Guru', 'Tamu'] as $type)
                                <option @selected(old('visitor_type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-700">Keperluan</span>
                        <select class="input" name="purpose" required>
                            @foreach(['Membaca', 'Meminjam Buku', 'Mengembalikan Buku', 'Mengerjakan Tugas', 'Akses Ebook', 'Riset', 'Diskusi'] as $purpose)
                                <option @selected(old('purpose', 'Membaca') === $purpose)>{{ $purpose }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="mb-1 block text-sm font-semibold text-slate-700">Catatan</span>
                        <input class="input" name="notes" placeholder="Opsional" value="{{ old('notes') }}">
                    </label>
                </div>

                <button class="btn-primary min-h-12" type="submit">Catat Kehadiran</button>
            </form>
        </div>
    </section>

    @if($recentVisits->isNotEmpty())
        <section class="py-8">
            <h2 class="section-title mb-4">Check-in Terbaru</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($recentVisits as $visit)
                    <div class="rounded-lg border border-slate-200 bg-white p-4">
                        <p class="font-semibold text-slate-950">{{ $visit->member?->name ?: $visit->guest_name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $visit->identity_number ?: $visit->member?->member_id ?: 'Nomor tidak tersedia' }} &middot; {{ $visit->purpose }}</p>
                        <p class="mt-3 text-xs font-semibold text-sky-700">{{ $visit->check_in_at->format('d M Y H:i') }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
