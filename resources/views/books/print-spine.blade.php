<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Nomor Punggung - {{ $book->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f4f5;
            color: #18181b;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
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

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            background: #ffffff;
            padding: 10mm;
        }

        .sheet-title {
            align-items: end;
            border-bottom: 1px solid #18181b;
            display: flex;
            justify-content: space-between;
            margin-bottom: 8mm;
            padding-bottom: 4mm;
        }

        .sheet-title h1 {
            font-size: 16pt;
            margin: 0;
        }

        .sheet-title p {
            color: #52525b;
            margin: 4px 0 0;
        }

        .labels {
            display: grid;
            gap: 6mm;
            grid-template-columns: repeat(4, 1fr);
        }

        .label {
            align-items: center;
            border: 1px dashed #71717a;
            display: flex;
            flex-direction: column;
            height: 58mm;
            justify-content: center;
            padding: 5mm 3mm;
            page-break-inside: avoid;
            text-align: center;
        }

        .library {
            border-bottom: 1px solid #18181b;
            font-size: 7pt;
            letter-spacing: .08em;
            margin-bottom: 4mm;
            padding-bottom: 2mm;
            text-transform: uppercase;
            width: 100%;
        }

        .call {
            font-size: 16pt;
            font-weight: 700;
            line-height: 1.25;
        }

        .copy {
            color: #52525b;
            font-size: 8pt;
            margin-top: 4mm;
        }

        .meta {
            color: #52525b;
            font-size: 7pt;
            margin-top: 2mm;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .toolbar {
                display: none;
            }

            .sheet {
                margin: 0;
                min-height: auto;
                padding: 0;
                width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print Nomor Punggung</button>
        <a href="{{ route('books.index') }}">Kembali</a>
    </div>

    <main class="sheet">
        <section class="sheet-title">
            <div>
                <h1>Label Nomor Punggung</h1>
                <p>{{ $book->title }} - {{ $ddcClass }}</p>
            </div>
            <p>{{ $book->stock_total }} label</p>
        </section>

        <section class="labels">
            @for($copy = 1; $copy <= max(1, $book->stock_total); $copy++)
                <article class="label">
                    <div class="library">{{ $appSettings['library_name'] }} {{ $appSettings['institution_name'] }}</div>
                    <div class="call">
                        <div>{{ $book->ddc }}</div>
                        <div>{{ $cutter }}</div>
                        <div>{{ $titleMark }}</div>
                    </div>
                    <div class="copy">C.{{ str_pad((string) $copy, 2, '0', STR_PAD_LEFT) }}{{ $book->rack ? ' / '.$book->rack : '' }}</div>
                    <div class="meta">{{ $book->call_number ?: $book->ddc.' '.$cutter }}</div>
                </article>
            @endfor
        </section>
    </main>
</body>
</html>
