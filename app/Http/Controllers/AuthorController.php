<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $authors = Author::query()
            ->withCount('books')
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('authors.index', [
            'authors' => $authors,
            'selectedAuthor' => null,
            'books' => null,
        ]);
    }

    public function show(Author $author)
    {
        return view('authors.index', [
            'authors' => Author::withCount('books')->orderBy('name')->paginate(15),
            'selectedAuthor' => $author,
            'books' => $author->books()->latest()->paginate(12),
        ]);
    }
}
