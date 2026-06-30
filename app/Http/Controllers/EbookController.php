<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EbookController extends Controller
{
    public function index(Request $request)
    {
        $ebooks = Ebook::query()
            ->when($request->search, fn ($query, $search) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('ebooks.index', [
            'ebooks' => $ebooks,
            'ebook' => null,
        ]);
    }

    public function create()
    {
        return view('ebooks.index', [
            'ebooks' => Ebook::latest()->paginate(10),
            'ebook' => new Ebook(['is_active' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['file_path'] = $request->file('ebook_file')?->store('ebooks/files', 'public');
        $data['cover_path'] = $request->file('cover')?->store('covers/ebooks', 'public');

        Ebook::create($data);

        return redirect()->route('ebooks.index')->with('status', 'Ebook berhasil ditambahkan.');
    }

    public function edit(Ebook $ebook)
    {
        return view('ebooks.index', [
            'ebooks' => Ebook::latest()->paginate(10),
            'ebook' => $ebook,
        ]);
    }

    public function update(Request $request, Ebook $ebook)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('ebook_file')) {
            if ($ebook->file_path) {
                Storage::disk('public')->delete($ebook->file_path);
            }
            $data['file_path'] = $request->file('ebook_file')->store('ebooks/files', 'public');
        }

        if ($request->hasFile('cover')) {
            if ($ebook->cover_path) {
                Storage::disk('public')->delete($ebook->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('covers/ebooks', 'public');
        }

        $ebook->update($data);

        return redirect()->route('ebooks.index')->with('status', 'Ebook berhasil diperbarui.');
    }

    public function destroy(Ebook $ebook)
    {
        if ($ebook->file_path) {
            Storage::disk('public')->delete($ebook->file_path);
        }
        if ($ebook->cover_path) {
            Storage::disk('public')->delete($ebook->cover_path);
        }

        $ebook->delete();

        return redirect()->route('ebooks.index')->with('status', 'Ebook berhasil dihapus.');
    }

    public function reader()
    {
        return view('ebooks.reader', [
            'ebooks' => Ebook::where('is_active', true)->latest()->paginate(12),
        ]);
    }

    public function read(Ebook $ebook)
    {
        abort_unless($ebook->is_active, 404);

        return view('ebooks.read', ['ebook' => $ebook]);
    }

    public function download(Ebook $ebook)
    {
        abort_unless($ebook->is_active, 404);
        $ebook->increment('download_count');

        if ($ebook->file_path) {
            return Storage::disk('public')->download($ebook->file_path);
        }

        return redirect()->away($ebook->external_url);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'external_url' => ['nullable', 'url', 'max:2048'],
            'ebook_file' => ['nullable', 'file', 'mimes:pdf,epub,doc,docx', 'max:51200'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
