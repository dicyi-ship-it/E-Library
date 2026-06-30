<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Info Buku - {{ $book->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 14mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f4f5;
            color: #18181b;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12pt;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 16px;
        }

        .toolbar button,
        .toolbar a {
            border: 1px solid #d4d4d8;
            border-radius: 6px;
            background: #ffffff;
            color: #18181b;
            cursor: pointer;
            font: inherit;
            padding: 8px 12px;
            text-decoration: none;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            background: #ffffff;
            padding: 14mm;
        }

        .header {
            border-bottom: 2px solid #18181b;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 22pt;
            line-height: 1.15;
            margin: 0;
        }

        .header p {
            margin: 6px 0 0;
            color: #52525b;
        }

        .badge {
            border: 1px solid #18181b;
            min-width: 34mm;
            padding: 8px;
            text-align: center;
        }

        .badge strong {
            display: block;
            font-size: 18pt;
        }

        .content {
            display: grid;
            grid-template-columns: 52mm 1fr;
            gap: 16px;
            margin-top: 16px;
        }

        .cover {
            align-items: center;
            border: 1px solid #d4d4d8;
            display: flex;
            height: 78mm;
            justify-content: center;
            overflow: hidden;
            width: 52mm;
        }

        .cover img {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .cover span {
            color: #71717a;
            font-weight: 700;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border-bottom: 1px solid #e4e4e7;
            padding: 7px 0;
            text-align: left;
            vertical-align: top;
        }

        th {
            color: #52525b;
            font-weight: 700;
            width: 38mm;
        }

        .description {
            border: 1px solid #d4d4d8;
            margin-top: 16px;
            min-height: 36mm;
            padding: 10px;
        }

        .footer {
            border-top: 1px solid #18181b;
            display: flex;
            justify-content: space-between;
            margin-top: 18mm;
            padding-top: 8px;
            color: #52525b;
            font-size: 10pt;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .toolbar {
                display: none;
            }

            .page {
                margin: 0;
                padding: 0;
                width: auto;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print Info A4</button>
        <a href="{{ route('books.index') }}">Kembali</a>
    </div>

    <main class="page">
        <section class="header">
            <div>
                <h1>{{ $book->title }}</h1>
                @if($book->subtitle)
                    <p>{{ $book->subtitle }}</p>
                @endif
                <p>{{ $appSettings['library_name'] }} {{ $appSettings['institution_name'] }} - Informasi Buku</p>
            </div>
            <div class="badge">
                <span>DDC</span>
                <strong>{{ $book->ddc }}</strong>
            </div>
        </section>

        <section class="content">
            <div class="cover">
                @if($book->cover_path)
                    <img src="{{ asset('storage/'.$book->cover_path) }}" alt="Cover {{ $book->title }}">
                @else
                    <span>NO COVER</span>
                @endif
            </div>

            <table>
                <tr><th>Nomor Panggil</th><td>{{ $book->call_number ?: '-' }}</td></tr>
                <tr><th>Kelas DDC</th><td>{{ $ddcClass }}</td></tr>
                <tr><th>Pengarang</th><td>{{ $book->author }}</td></tr>
                <tr><th>ISBN</th><td>{{ $book->isbn ?: '-' }}</td></tr>
                <tr><th>Penerbit</th><td>{{ $book->publisher ?: '-' }}</td></tr>
                <tr><th>Tahun</th><td>{{ $book->publication_year ?: '-' }}</td></tr>
                <tr><th>Edisi</th><td>{{ $book->edition ?: '-' }}</td></tr>
                <tr><th>Bahasa</th><td>{{ $book->language }}</td></tr>
                <tr><th>Kategori</th><td>{{ $book->category ?: '-' }}</td></tr>
                <tr><th>Lokasi</th><td>{{ $book->location ?: '-' }}{{ $book->rack ? ' / Rak '.$book->rack : '' }}</td></tr>
                <tr><th>Stok</th><td>{{ $book->stock_available }} tersedia dari {{ $book->stock_total }} eksemplar</td></tr>
            </table>
        </section>

        <section class="description">
            <strong>Deskripsi</strong>
            <p>{{ $book->description ?: 'Tidak ada deskripsi.' }}</p>
        </section>

        <section class="footer">
            <span>Dicetak: {{ now()->format('d M Y H:i') }}</span>
            <span>{{ config('app.url') }}</span>
        </section>
    </main>
</body>
</html>
