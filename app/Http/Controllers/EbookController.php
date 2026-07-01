<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function reader(Request $request)
    {
        return view('ebooks.reader', [
            'ebooks' => $request->user()
                ->ebooks()
                ->where('is_active', true)
                ->orderByDesc('ebook_user.updated_at')
                ->paginate(12),
        ]);
    }

    public function read(Request $request, Ebook $ebook)
    {
        abort_unless($ebook->is_active, 404);
        $this->rememberEbook($request, $ebook, 'read');

        return view('ebooks.read', ['ebook' => $ebook]);
    }

    public function download(Request $request, Ebook $ebook)
    {
        abort_unless($ebook->is_active, 404);
        $this->rememberEbook($request, $ebook, 'download');
        $ebook->increment('download_count');

        if ($ebook->file_path) {
            return Storage::disk('public')->download($ebook->file_path);
        }

        return redirect()->away($ebook->external_url);
    }

    public function removeFromReader(Request $request, Ebook $ebook)
    {
        $request->user()->ebooks()->detach($ebook->id);

        return redirect()->route('ebooks.reader')->with('status', 'Ebook dihapus dari daftar Anda.');
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

    private function rememberEbook(Request $request, Ebook $ebook, string $action): void
    {
        $user = $request->user();

        if (! $user || $user->isAdmin()) {
            return;
        }

        $now = now();
        $values = [
            'updated_at' => $now,
        ];

        if ($action === 'read') {
            $values['last_read_at'] = $now;
            $values['read_count'] = DB::raw('read_count + 1');
        }

        if ($action === 'download') {
            $values['last_downloaded_at'] = $now;
            $values['download_count'] = DB::raw('download_count + 1');
        }

        $updated = DB::table('ebook_user')
            ->where('user_id', $user->id)
            ->where('ebook_id', $ebook->id)
            ->update($values);

        if ($updated) {
            return;
        }

        DB::table('ebook_user')->insert([
            'user_id' => $user->id,
            'ebook_id' => $ebook->id,
            'first_accessed_at' => $now,
            'last_read_at' => $action === 'read' ? $now : null,
            'last_downloaded_at' => $action === 'download' ? $now : null,
            'read_count' => $action === 'read' ? 1 : 0,
            'download_count' => $action === 'download' ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
