<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi {{ $appSettings['library_name'] }} {{ $appSettings['institution_name'] }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased" data-kiosk-default-tab="{{ old('form_mode', 'checkin') }}">
    <main class="min-h-screen bg-[radial-gradient(circle_at_top_left,#e0f2fe_0,#f8fafc_34%,#ffffff_68%)]">
        <div class="mx-auto grid min-h-screen max-w-7xl gap-6 px-4 py-5 lg:grid-cols-[.9fr_1.1fr] lg:px-8">
            <section class="flex flex-col justify-between rounded-lg bg-slate-950 p-6 text-white shadow-xl shadow-sky-950/20">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-md bg-sky-600 text-base font-bold">{{ $appSettings['logo_text'] }}</span>
                        <div>
                            <p class="text-sm font-semibold text-sky-200">{{ $appSettings['institution_name'] }}</p>
                            <h1 class="text-2xl font-bold">Absensi {{ $appSettings['library_name'] }}</h1>
                        </div>
                    </div>

                    <div class="mt-10">
                        <p class="text-sm font-semibold uppercase tracking-normal text-sky-200">{{ now()->format('d M Y') }}</p>
                        <p class="mt-2 text-6xl font-bold leading-none" data-kiosk-clock>{{ now()->format('H:i') }}</p>
                        <p class="mt-4 max-w-md text-lg leading-8 text-slate-300">Check-in pengunjung dan registrasi anggota baru dalam satu layar.</p>
                    </div>
                </div>

                <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                    <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                        <span class="block text-4xl font-bold">{{ $todayVisits }}</span>
                        <small class="mt-1 block text-sm text-slate-300">Kunjungan hari ini</small>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/10 p-5">
                        <span class="block text-4xl font-bold">{{ $activeVisitors }}</span>
                        <small class="mt-1 block text-sm text-slate-300">Pengunjung aktif</small>
                    </div>
                </div>

                <div class="mt-8">
                    <p class="mb-3 text-sm font-semibold text-slate-300">Check-in terbaru</p>
                    <div class="space-y-2">
                        @forelse($recentVisits->take(4) as $visit)
                            <div class="rounded-md border border-white/10 bg-white/10 px-4 py-3">
                                <p class="font-semibold">{{ $visit->member?->name ?: $visit->guest_name }}</p>
                                <p class="mt-1 text-sm text-slate-300">{{ $visit->identity_number ?: $visit->member?->member_id ?: '-' }} &middot; {{ $visit->check_in_at->format('H:i') }}</p>
                            </div>
                        @empty
                            <div class="rounded-md border border-white/10 bg-white/10 px-4 py-3 text-sm text-slate-300">Belum ada check-in hari ini.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="flex min-h-full flex-col rounded-lg border border-sky-100 bg-white p-5 shadow-xl shadow-sky-100">
                @if(session('status'))
                    <div class="mb-4 rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                        <strong>Periksa kembali data:</strong>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-5 grid grid-cols-2 gap-2 rounded-lg bg-slate-100 p-1" data-kiosk-tabs>
                    <button class="kiosk-tab kiosk-tab-active" type="button" data-kiosk-tab="checkin">Check-in</button>
                    <button class="kiosk-tab" type="button" data-kiosk-tab="register">Registrasi Baru</button>
                </div>

                <div class="grid flex-1 content-start gap-5" data-kiosk-panel="checkin" data-qr-attendance>
                    <div class="rounded-lg border border-slate-200 bg-slate-950 p-4 text-white">
                        <div class="aspect-video overflow-hidden rounded-md bg-slate-900">
                            <video class="hidden h-full w-full object-cover" playsinline muted data-qr-video></video>
                            <div class="grid h-full place-items-center p-6 text-center" data-qr-placeholder>
                                <div>
                                    <p class="text-sm font-semibold text-sky-200">QR Scanner</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-300">Scan kartu anggota atau input nomor induk manual.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button class="btn-light" type="button" data-qr-start>Mulai Scan</button>
                            <button class="btn-dark" type="button" data-qr-stop>Berhenti</button>
                        </div>
                        <p class="mt-3 text-sm text-slate-300" data-qr-status>Scanner siap digunakan.</p>
                    </div>

                    <form class="grid gap-4" method="POST" action="{{ route('attendance.kiosk.store') }}">
                        @csrf
                        <input type="hidden" name="kiosk_mode" value="1">
                        <input type="hidden" name="attendance_source" value="manual" data-qr-source>
                        <input type="hidden" name="form_mode" value="checkin">

                        <label>
                            <span class="mb-1 block text-sm font-semibold text-slate-700">Nomor Induk</span>
                            <input class="input min-h-14 text-lg" name="identity_number" data-qr-identity placeholder="NIM / NIS / NIDN / NUPTK / Nomor anggota" value="{{ old('identity_number') }}" required autofocus>
                        </label>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <label>
                                <span class="mb-1 block text-sm font-semibold text-slate-700">Nama</span>
                                <input class="input" name="visitor_name" placeholder="Opsional" value="{{ old('visitor_name') }}">
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

                        <button class="btn-primary min-h-14 text-base" type="submit">Catat Kehadiran</button>
                    </form>
                </div>

                <form class="hidden flex-1 content-start gap-4" method="POST" action="{{ route('attendance.kiosk.register') }}" data-kiosk-panel="register">
                    @csrf
                    <input type="hidden" name="form_mode" value="register">

                    <div class="grid gap-3 sm:grid-cols-2">
                        <label class="sm:col-span-2">
                            <span class="mb-1 block text-sm font-semibold text-slate-700">Nama Lengkap</span>
                            <input class="input min-h-12" name="name" placeholder="Nama sesuai kartu identitas kampus" value="{{ old('name') }}" required>
                        </label>
                        <label>
                            <span class="mb-1 block text-sm font-semibold text-slate-700">Nomor Induk</span>
                            <input class="input min-h-12" name="identity_number" placeholder="NIM / NIS / NIDN / NUPTK" value="{{ old('identity_number') }}" required>
                        </label>
                        <label>
                            <span class="mb-1 block text-sm font-semibold text-slate-700">Jenis Anggota</span>
                            <select class="input min-h-12" name="level" required>
                                <option value="">Pilih jenis</option>
                                @foreach(['Mahasiswa', 'Dosen', 'Tenaga Kependidikan', 'Siswa', 'Guru', 'Tamu'] as $level)
                                    <option @selected(old('level') === $level)>{{ $level }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span class="mb-1 block text-sm font-semibold text-slate-700">Email</span>
                            <input class="input" type="email" name="email" placeholder="Opsional" value="{{ old('email') }}">
                        </label>
                        <label>
                            <span class="mb-1 block text-sm font-semibold text-slate-700">No. Telepon</span>
                            <input class="input" name="phone" placeholder="Opsional" value="{{ old('phone') }}">
                        </label>
                        <label>
                            <span class="mb-1 block text-sm font-semibold text-slate-700">Fakultas / Unit</span>
                            <input class="input" name="faculty" placeholder="{{ $appSettings['institution_name'] }}" value="{{ old('faculty') }}">
                        </label>
                        <label>
                            <span class="mb-1 block text-sm font-semibold text-slate-700">Program Studi / Bagian</span>
                            <input class="input" name="study_program" placeholder="Umum" value="{{ old('study_program') }}">
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

                    <button class="btn-primary min-h-14 text-base" type="submit">Daftar & Catat Hadir</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
