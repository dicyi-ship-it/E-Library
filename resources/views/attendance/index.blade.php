@extends('layouts.app')

@section('title', 'Kehadiran')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Kehadiran Perpustakaan</h1>
            <p class="page-subtitle">Catatan kunjungan anggota, tamu, dan check-in publik.</p>
        </div>
        <a class="btn-secondary" href="{{ route('attendance.kiosk') }}" target="_blank" rel="noopener">Buka Kiosk Absensi</a>
    </div>

    <form class="panel mb-8 grid gap-4" method="POST" action="{{ route('attendance.store') }}">
        @csrf
        <h2 class="section-title">Check-in Manual</h2>
        <div class="grid gap-4 md:grid-cols-5">
            <select class="input" name="member_id">
                <option value="">Pilih anggota</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>{{ $member->name }} &middot; {{ $member->member_id }}</option>
                @endforeach
            </select>
            <input class="input" name="guest_name" placeholder="Nama tamu" value="{{ old('guest_name') }}">
            <input class="input" name="identity_number" placeholder="NIM/NIS/NIDN/NUPTK" value="{{ old('identity_number') }}">
            <select class="input" name="visitor_type">
                <option value="">Jenis pengunjung</option>
                @foreach(['Mahasiswa', 'Dosen', 'Tenaga Kependidikan', 'Siswa', 'Guru', 'Tamu'] as $type)
                    <option @selected(old('visitor_type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
            <select class="input" name="purpose" required>
                @foreach(['Membaca', 'Meminjam Buku', 'Mengembalikan Buku', 'Mengerjakan Tugas', 'Akses Ebook', 'Riset'] as $purpose)
                    <option @selected(old('purpose') === $purpose)>{{ $purpose }}</option>
                @endforeach
            </select>
            <input class="input md:col-span-5" name="notes" placeholder="Catatan" value="{{ old('notes') }}">
        </div>
        <button class="btn-primary w-fit" type="submit">Catat Kehadiran</button>
    </form>

    <section class="panel">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengunjung</th>
                        <th>Keperluan</th>
                        <th>Masuk</th>
                        <th>Keluar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $visit->member?->name ?: $visit->guest_name }}</p>
                                <p class="text-sm text-zinc-500">{{ $visit->identity_number ?: $visit->member?->member_id ?: 'Tamu' }}</p>
                                <p class="text-xs text-zinc-400">{{ $visit->visitor_type ?: '-' }} &middot; {{ strtoupper($visit->attendance_source ?: 'manual') }}</p>
                            </td>
                            <td>{{ $visit->purpose }}<br><span class="text-sm text-zinc-500">{{ $visit->notes }}</span></td>
                            <td>{{ $visit->check_in_at->format('d M Y H:i') }}</td>
                            <td>{{ $visit->check_out_at?->format('d M Y H:i') ?: '-' }}</td>
                            <td>
                                @if(! $visit->check_out_at)
                                    <form method="POST" action="{{ route('attendance.checkout', $visit) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn-mini" type="submit">Check-out</button>
                                    </form>
                                @else
                                    <span class="badge">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-zinc-500">Belum ada kunjungan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $visits->links() }}</div>
    </section>
@endsection
