<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function index(Request $request)
    {
        $publishers = Publisher::query()
            ->withCount('books')
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('publishers.index', [
            'publishers' => $publishers,
            'selectedPublisher' => null,
            'books' => null,
        ]);
    }

    public function show(Publisher $publisher)
    {
        return view('publishers.index', [
            'publishers' => Publisher::withCount('books')->orderBy('name')->paginate(15),
            'selectedPublisher' => $publisher,
            'books' => $publisher->books()->latest()->paginate(12),
        ]);
    }
}
