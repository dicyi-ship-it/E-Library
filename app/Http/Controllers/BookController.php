<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    private const DDC_CLASSES = [
        '000' => 'Karya umum, komputer, dan informasi',
        '100' => 'Filsafat dan psikologi',
        '200' => 'Agama',
        '300' => 'Ilmu sosial',
        '400' => 'Bahasa',
        '500' => 'Sains',
        '600' => 'Teknologi dan ilmu terapan',
        '700' => 'Seni dan rekreasi',
        '800' => 'Sastra',
        '900' => 'Sejarah dan geografi',
    ];

    public function index(Request $request)
    {
        $books = Book::query()
            ->with(['authorRecord', 'publisherRecord'])
            ->when($request->search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('ddc', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('books.index', [
            'books' => $books,
            'book' => null,
            'ddcClasses' => self::DDC_CLASSES,
            'authors' => Author::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('books.index', [
            'books' => Book::with(['authorRecord', 'publisherRecord'])->latest()->paginate(10),
            'book' => new Book,
            'ddcClasses' => self::DDC_CLASSES,
            'authors' => Author::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data = $this->syncIndexedPeople($data);
        $data['cover_path'] = $request->file('cover')?->store('covers/books', 'public');
        $data['call_number'] = $data['call_number'] ?: $this->buildCallNumber($data);
        $data['category'] = $data['category'] ?: $this->ddcClassName($data['ddc']);
        $data['stock_available'] = $data['stock_total'];

        Book::create($data);

        return redirect()->route('books.index')->with('status', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book)
    {
        return view('books.index', [
            'books' => Book::with(['authorRecord', 'publisherRecord'])->latest()->paginate(10),
            'book' => $book,
            'ddcClasses' => self::DDC_CLASSES,
            'authors' => Author::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $data = $this->validated($request, $book);
        $data = $this->syncIndexedPeople($data);
        $borrowed = max(0, $book->stock_total - $book->stock_available);
        $data['call_number'] = $data['call_number'] ?: $this->buildCallNumber($data);
        $data['category'] = $data['category'] ?: $this->ddcClassName($data['ddc']);
        $data['stock_available'] = max(0, $data['stock_total'] - $borrowed);

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('covers/books', 'public');
        }

        $book->update($data);

        return redirect()->route('books.index')->with('status', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }

        $book->delete();

        return redirect()->route('books.index')->with('status', 'Buku berhasil dihapus.');
    }

    public function printInfo(Book $book)
    {
        return view('books.print-info', [
            'book' => $book,
            'ddcClass' => $this->ddcClassName($book->ddc),
        ]);
    }

    public function printSpine(Book $book)
    {
        return view('books.print-spine', [
            'book' => $book,
            'ddcClass' => $this->ddcClassName($book->ddc),
            'cutter' => $this->cutterCode($book->author),
            'titleMark' => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $book->title), 0, 1)) ?: 'B',
        ]);
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255', 'unique:books,isbn,'.($book?->id ?? 'NULL')],
            'ddc' => ['required', 'string', 'max:50', 'regex:/^(0[0-9]{2}|[1-9][0-9]{2})(\.[0-9]+)?$/'],
            'call_number' => ['nullable', 'string', 'max:100'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'publication_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'edition' => ['nullable', 'string', 'max:100'],
            'language' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:150'],
            'rack' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'stock_total' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'max:100'],
            'cover' => [$book ? 'nullable' : 'required', 'image', 'max:2048'],
        ], [
            'ddc.regex' => 'DDC harus memakai format 000-999 dengan desimal opsional, contoh 020 atau 005.13.',
            'cover.required' => 'Foto sampul wajib diunggah saat menambah buku baru.',
            'cover.image' => 'Cover harus berupa file gambar.',
        ]);
    }

    private function syncIndexedPeople(array $data): array
    {
        $authorName = trim($data['author']);
        $author = Author::firstOrCreate(['name' => $authorName]);

        $data['author'] = $author->name;
        $data['author_id'] = $author->id;

        $publisherName = trim((string) ($data['publisher'] ?? ''));

        if ($publisherName !== '') {
            $publisher = Publisher::firstOrCreate(['name' => $publisherName]);
            $data['publisher'] = $publisher->name;
            $data['publisher_id'] = $publisher->id;
        } else {
            $data['publisher'] = null;
            $data['publisher_id'] = null;
        }

        return $data;
    }

    private function buildCallNumber(array $data): string
    {
        return trim(implode(' ', array_filter([
            $data['ddc'],
            $this->cutterCode($data['author']),
            ! empty($data['publication_year']) ? (string) $data['publication_year'] : null,
        ])));
    }

    private function cutterCode(?string $author): string
    {
        $clean = strtoupper(preg_replace('/[^A-Za-z]/', '', $author ?: 'BUKU'));

        return substr($clean, 0, 3) ?: 'BUK';
    }

    private function ddcClassName(string $ddc): string
    {
        $hundreds = floor(((float) $ddc) / 100) * 100;
        $key = str_pad((string) $hundreds, 3, '0', STR_PAD_LEFT);

        return self::DDC_CLASSES[$key] ?? 'Klasifikasi DDC';
    }
}
