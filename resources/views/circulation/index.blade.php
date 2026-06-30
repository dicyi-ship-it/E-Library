@extends('layouts.app')

@section('title', 'Sirkulasi')

@section('content')
    <div class="mb-8">
        <h1 class="page-title">Sirkulasi Buku</h1>
        <p class="page-subtitle">Peminjaman, pengembalian, jatuh tempo, dan denda.</p>
    </div>

    <form class="panel mb-8 grid gap-4" method="POST" action="{{ route('circulation.store') }}">
        @csrf
        <h2 class="section-title">Peminjaman Baru</h2>

        @php
            $selectedBook = $books->firstWhere('id', (int) old('book_id'));
            $selectedMember = $members->firstWhere('id', (int) old('member_id'));
        @endphp

        <div class="grid gap-4 md:grid-cols-5">
            <div class="searchable-select md:col-span-2" data-searchable-select>
                <input type="hidden" name="book_id" value="{{ old('book_id') }}" required data-searchable-value>
                <label class="mb-1 block text-sm font-semibold text-slate-700" for="book-search">Buku</label>
                <input
                    id="book-search"
                    class="input"
                    type="text"
                    autocomplete="off"
                    placeholder="Ketik judul, pengarang, ISBN, DDC, atau rak"
                    value="{{ $selectedBook ? $selectedBook->title.' - stok '.$selectedBook->stock_available : '' }}"
                    data-searchable-input
                >
                <div class="searchable-options hidden" data-searchable-options>
                    @foreach($books as $book)
                        <button
                            class="searchable-option"
                            type="button"
                            data-searchable-option
                            data-value="{{ $book->id }}"
                            data-label="{{ $book->title }} - stok {{ $book->stock_available }}"
                            data-search="{{ strtolower($book->title.' '.$book->author.' '.$book->isbn.' '.$book->ddc.' '.$book->call_number.' '.$book->rack.' '.$book->category) }}"
                        >
                            <span class="font-semibold text-slate-950">{{ $book->title }}</span>
                            <span class="mt-1 block text-xs text-slate-500">{{ $book->author }} &middot; DDC {{ $book->ddc }} &middot; Rak {{ $book->rack ?: '-' }} &middot; stok {{ $book->stock_available }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-slate-500" data-searchable-hint>Ketik untuk mencari, lalu pilih buku dari daftar.</p>
            </div>

            <div class="searchable-select md:col-span-2" data-searchable-select>
                <input type="hidden" name="member_id" value="{{ old('member_id') }}" required data-searchable-value>
                <label class="mb-1 block text-sm font-semibold text-slate-700" for="member-search">Anggota</label>
                <input
                    id="member-search"
                    class="input"
                    type="text"
                    autocomplete="off"
                    placeholder="Ketik nama, nomor anggota, NIM/NIDN, atau prodi"
                    value="{{ $selectedMember ? $selectedMember->name.' - '.($selectedMember->member_id ?: $selectedMember->identity_number) : '' }}"
                    data-searchable-input
                >
                <div class="searchable-options hidden" data-searchable-options>
                    @foreach($members as $member)
                        <button
                            class="searchable-option"
                            type="button"
                            data-searchable-option
                            data-value="{{ $member->id }}"
                            data-label="{{ $member->name }} - {{ $member->member_id ?: $member->identity_number }}"
                            data-search="{{ strtolower($member->name.' '.$member->email.' '.$member->member_id.' '.$member->identity_number.' '.$member->faculty.' '.$member->department.' '.$member->study_program.' '.$member->level) }}"
                        >
                            <span class="font-semibold text-slate-950">{{ $member->name }}</span>
                            <span class="mt-1 block text-xs text-slate-500">{{ $member->member_id ?: '-' }} &middot; {{ $member->identity_number ?: '-' }} &middot; {{ $member->study_program ?: $member->level }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-slate-500" data-searchable-hint>Ketik untuk mencari, lalu pilih anggota dari daftar.</p>
            </div>

            <label>
                <span class="mb-1 block text-sm font-semibold text-slate-700">Tanggal Pinjam</span>
                <input class="input" type="date" name="borrowed_at" value="{{ old('borrowed_at', today()->toDateString()) }}" required>
            </label>
            <label>
                <span class="mb-1 block text-sm font-semibold text-slate-700">Jatuh Tempo</span>
                <input class="input" type="date" name="due_at" value="{{ old('due_at', today()->addDays(7)->toDateString()) }}" required>
            </label>
            <label class="md:col-span-4">
                <span class="mb-1 block text-sm font-semibold text-slate-700">Catatan</span>
                <input class="input" name="notes" placeholder="Catatan opsional" value="{{ old('notes') }}">
            </label>
        </div>

        <button class="btn-primary w-fit" type="submit">Catat Peminjaman</button>
    </form>

    <section class="panel">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->loan_code }}<br><span class="badge">{{ $loan->status }}</span></td>
                            <td>{{ $loan->member->name }}<br><span class="text-sm text-zinc-500">{{ $loan->member->member_id }}</span></td>
                            <td>{{ $loan->book->title }}<br><span class="text-sm text-zinc-500">{{ $loan->book->ddc }}</span></td>
                            <td>{{ $loan->borrowed_at->format('d M Y') }}</td>
                            <td>{{ $loan->due_at->format('d M Y') }}</td>
                            <td>Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($loan->status !== 'returned')
                                    <form method="POST" action="{{ route('circulation.return', $loan) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn-mini" type="submit">Kembalikan</button>
                                    </form>
                                @else
                                    {{ $loan->returned_at?->format('d M Y') }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-zinc-500">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $loans->links() }}</div>
    </section>
@endsection
