@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Ringkasan layanan perpustakaan hari ini.</p>
        </div>
        <a class="btn-primary" href="{{ route('circulation.index') }}">Transaksi Sirkulasi</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <div class="metric"><span>{{ $bookCount }}</span><small>Judul buku</small></div>
        <div class="metric"><span>{{ $availableStock }}</span><small>Stok tersedia</small></div>
        <div class="metric"><span>{{ $memberCount }}</span><small>Anggota</small></div>
        <div class="metric"><span>{{ $todayVisits }}</span><small>Kehadiran</small></div>
        <div class="metric"><span>{{ $activeLoans }}</span><small>Dipinjam</small></div>
        <div class="metric"><span>{{ $overdueLoans }}</span><small>Terlambat</small></div>
    </div>

    <section class="mt-8 panel">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="section-title">Transaksi Terbaru</h2>
            <a class="text-sm font-medium text-emerald-700" href="{{ route('circulation.index') }}">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Jatuh tempo</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLoans as $loan)
                        <tr>
                            <td>{{ $loan->loan_code }}</td>
                            <td>{{ $loan->member->name }}</td>
                            <td>{{ $loan->book->title }}</td>
                            <td>{{ $loan->due_at?->format('d M Y') }}</td>
                            <td><span class="badge">{{ $loan->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-zinc-500">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
