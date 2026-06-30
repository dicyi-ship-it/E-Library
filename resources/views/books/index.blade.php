@extends('layouts.app')

@section('title', 'Master Buku')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Master Buku</h1>
            <p class="page-subtitle">Katalog fisik dengan DDC, nomor panggil, stok, rak, dan foto sampul.</p>
        </div>
        <a class="btn-primary" href="{{ route('books.create') }}">Tambah Buku</a>
    </div>

    @if($book)
        <form class="panel mb-8 grid gap-4" method="POST" enctype="multipart/form-data" action="{{ $book->exists ? route('books.update', $book) : route('books.store') }}">
            @csrf
            @if($book->exists) @method('PUT') @endif
            <h2 class="section-title">{{ $book->exists ? 'Edit Buku' : 'Buku Baru' }}</h2>

            <div class="rounded-md border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">
                <p class="font-semibold">Aturan ringkas DDC</p>
                <p class="mt-1">Isi DDC dalam format 000-999 dengan desimal opsional, contoh 020, 297.3, atau 005.13. Jika nomor panggil dikosongkan, sistem membuatnya dari DDC, tiga huruf awal pengarang, dan tahun terbit.</p>
                <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach($ddcClasses as $code => $name)
                        <span class="rounded bg-white px-2 py-1">{{ $code }} - {{ $name }}</span>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <input class="input md:col-span-2" name="title" placeholder="Judul" value="{{ old('title', $book->title) }}" required>
                <input class="input" name="subtitle" placeholder="Subjudul" value="{{ old('subtitle', $book->subtitle) }}">
                <input class="input" name="isbn" placeholder="ISBN" value="{{ old('isbn', $book->isbn) }}">
                <input class="input" name="ddc" pattern="(0[0-9]{2}|[1-9][0-9]{2})(\.[0-9]+)?" placeholder="DDC, contoh 005.13" value="{{ old('ddc', $book->ddc) }}" required>
                <input class="input" name="call_number" placeholder="Nomor panggil, kosongkan untuk otomatis" value="{{ old('call_number', $book->call_number) }}">
                <label class="md:col-span-2">
                    <span class="mb-1 block text-sm font-semibold text-slate-700">Penulis</span>
                    <input class="input" name="author" list="author-options" placeholder="Pilih penulis lama atau ketik penulis baru" value="{{ old('author', $book->author) }}" required>
                </label>
                <label>
                    <span class="mb-1 block text-sm font-semibold text-slate-700">Penerbit</span>
                    <input class="input" name="publisher" list="publisher-options" placeholder="Pilih penerbit lama atau ketik penerbit baru" value="{{ old('publisher', $book->publisher) }}">
                </label>
                <datalist id="author-options">
                    @foreach($authors as $author)
                        <option value="{{ $author->name }}"></option>
                    @endforeach
                </datalist>
                <datalist id="publisher-options">
                    @foreach($publishers as $publisher)
                        <option value="{{ $publisher->name }}"></option>
                    @endforeach
                </datalist>
                <input class="input" type="number" name="publication_year" placeholder="Tahun" value="{{ old('publication_year', $book->publication_year) }}">
                <input class="input" name="edition" placeholder="Edisi" value="{{ old('edition', $book->edition) }}">
                <input class="input" name="language" placeholder="Bahasa" value="{{ old('language', $book->language ?: 'Indonesia') }}" required>
                <input class="input" name="category" placeholder="Kategori, otomatis dari DDC bila kosong" value="{{ old('category', $book->category) }}">
                <input class="input" name="rack" placeholder="Rak" value="{{ old('rack', $book->rack) }}">
                <input class="input" name="location" placeholder="Lokasi" value="{{ old('location', $book->location) }}">
                <input class="input" type="number" min="0" name="stock_total" placeholder="Stok total" value="{{ old('stock_total', $book->stock_total ?: 1) }}" required>
                <select class="input" name="status" required>
                    @foreach(['available' => 'Tersedia', 'maintenance' => 'Perawatan', 'archived' => 'Arsip'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $book->status ?: 'available') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <label class="block">
                    <span class="mb-1 block text-sm font-medium text-zinc-700">Foto sampul {{ $book->exists ? '(opsional jika tidak diganti)' : '(wajib)' }}</span>
                    <input class="input" type="file" name="cover" accept="image/*" @required(! $book->exists)>
                </label>
                @if($book->cover_path)
                    <div class="rounded-md border border-zinc-200 p-3">
                        <img class="h-28 w-20 rounded object-cover" src="{{ asset('storage/'.$book->cover_path) }}" alt="Cover {{ $book->title }}">
                        <p class="mt-2 text-xs text-zinc-500">Cover saat ini</p>
                    </div>
                @endif
                <textarea class="input md:col-span-3" name="description" rows="3" placeholder="Deskripsi">{{ old('description', $book->description) }}</textarea>
            </div>
            <div class="flex gap-3">
                <button class="btn-primary" type="submit">Simpan</button>
                <a class="btn-secondary" href="{{ route('books.index') }}">Batal</a>
            </div>
        </form>
    @endif

    <section class="panel">
        <form class="mb-4 flex gap-3" method="GET">
            <input class="input" name="search" placeholder="Cari judul, penulis, penerbit, ISBN, DDC" value="{{ request('search') }}">
            <button class="btn-secondary" type="submit">Cari</button>
        </form>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Judul</th>
                        <th>DDC</th>
                        <th>Stok</th>
                        <th>Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $item)
                        <tr>
                            <td>
                                @if($item->cover_path)
                                    <img class="h-16 w-12 rounded object-cover" src="{{ asset('storage/'.$item->cover_path) }}" alt="Cover {{ $item->title }}">
                                @else
                                    <div class="grid h-16 w-12 place-items-center rounded bg-zinc-200 text-xs font-semibold text-zinc-600">DDC</div>
                                @endif
                            </td>
                            <td>
                                <a class="font-medium text-slate-950 hover:text-sky-700 hover:underline" href="{{ route('catalog.books.show', $item) }}">{{ $item->title }}</a>
                                <p class="text-sm text-zinc-500">
                                    @if($item->authorRecord)
                                        <a class="text-sky-700 hover:underline" href="{{ route('authors.show', $item->authorRecord) }}">{{ $item->author }}</a>
                                    @else
                                        {{ $item->author }}
                                    @endif
                                    @if($item->publisher)
                                        &middot;
                                        @if($item->publisherRecord)
                                            <a class="text-sky-700 hover:underline" href="{{ route('publishers.show', $item->publisherRecord) }}">{{ $item->publisher }}</a>
                                        @else
                                            {{ $item->publisher }}
                                        @endif
                                    @endif
                                </p>
                                <p class="text-sm text-zinc-500">{{ $item->isbn }}</p>
                            </td>
                            <td>{{ $item->ddc }}<br><span class="text-sm text-zinc-500">{{ $item->call_number }}</span></td>
                            <td>{{ $item->stock_available }}/{{ $item->stock_total }}</td>
                            <td>{{ $item->rack }}<br><span class="text-sm text-zinc-500">{{ $item->location }}</span></td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <a class="btn-mini" href="{{ route('catalog.books.show', $item) }}">Lihat Detail</a>
                                    <a class="btn-mini" href="{{ route('books.print-info', $item) }}" target="_blank" rel="noopener">Info A4</a>
                                    <a class="btn-mini" href="{{ route('books.print-spine', $item) }}" target="_blank" rel="noopener">No. Punggung</a>
                                    <a class="btn-mini" href="{{ route('books.edit', $item) }}">Edit</a>
                                    <form method="POST" action="{{ route('books.destroy', $item) }}" onsubmit="return confirm('Hapus buku ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-mini-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-zinc-500">Belum ada data buku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $books->links() }}</div>
    </section>
@endsection
