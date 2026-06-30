@extends('layouts.app')

@section('title', 'Master Ebook')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="page-title">Master Ebook</h1>
            <p class="page-subtitle">Koleksi digital untuk member terdaftar.</p>
        </div>
        <a class="btn-primary" href="{{ route('ebooks.create') }}">Tambah Ebook</a>
    </div>

    @if($ebook)
        <form class="panel mb-8 grid gap-4" method="POST" enctype="multipart/form-data" action="{{ $ebook->exists ? route('ebooks.update', $ebook) : route('ebooks.store') }}">
            @csrf
            @if($ebook->exists) @method('PUT') @endif
            <h2 class="section-title">{{ $ebook->exists ? 'Edit Ebook' : 'Ebook Baru' }}</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <input class="input md:col-span-2" name="title" placeholder="Judul" value="{{ old('title', $ebook->title) }}" required>
                <input class="input" name="author" placeholder="Penulis" value="{{ old('author', $ebook->author) }}">
                <input class="input" name="category" placeholder="Kategori" value="{{ old('category', $ebook->category) }}">
                <input class="input md:col-span-2" type="url" name="external_url" placeholder="URL eksternal" value="{{ old('external_url', $ebook->external_url) }}">
                <input class="input" type="file" name="ebook_file" accept=".pdf,.epub,.doc,.docx">
                <input class="input" type="file" name="cover" accept="image/*">
                <label class="flex items-center gap-2 text-sm text-zinc-600">
                    <input class="rounded border-zinc-300" type="checkbox" name="is_active" value="1" @checked(old('is_active', $ebook->is_active ?? true))>
                    Aktif
                </label>
                <textarea class="input md:col-span-3" name="description" rows="3" placeholder="Deskripsi">{{ old('description', $ebook->description) }}</textarea>
            </div>
            <div class="flex gap-3">
                <button class="btn-primary" type="submit">Simpan</button>
                <a class="btn-secondary" href="{{ route('ebooks.index') }}">Batal</a>
            </div>
        </form>
    @endif

    <section class="panel">
        <form class="mb-4 flex gap-3" method="GET">
            <input class="input" name="search" placeholder="Cari judul, penulis, kategori" value="{{ request('search') }}">
            <button class="btn-secondary" type="submit">Cari</button>
        </form>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Ebook</th>
                        <th>Akses</th>
                        <th>Unduhan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ebooks as $item)
                        <tr>
                            <td>
                                @if($item->cover_path)
                                    <img class="h-16 w-12 rounded object-cover" src="{{ asset('storage/'.$item->cover_path) }}" alt="Cover {{ $item->title }}">
                                @else
                                    <div class="grid h-16 w-12 place-items-center rounded bg-sky-100 text-xs font-semibold text-sky-700">PDF</div>
                                @endif
                            </td>
                            <td>
                                <p class="font-medium">{{ $item->title }}</p>
                                <p class="text-sm text-zinc-500">{{ $item->author }} · {{ $item->category }}</p>
                            </td>
                            <td>{{ $item->file_path ? 'File lokal' : 'URL eksternal' }}</td>
                            <td>{{ $item->download_count }}</td>
                            <td><span class="badge">{{ $item->is_active ? 'aktif' : 'nonaktif' }}</span></td>
                            <td class="whitespace-nowrap">
                                <a class="btn-mini" href="{{ route('ebooks.edit', $item) }}">Edit</a>
                                <form class="inline" method="POST" action="{{ route('ebooks.destroy', $item) }}" onsubmit="return confirm('Hapus ebook ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-mini-danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-zinc-500">Belum ada ebook.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $ebooks->links() }}</div>
    </section>
@endsection
