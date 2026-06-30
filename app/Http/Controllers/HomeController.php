<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Ebook;
use App\Models\Loan;
use App\Models\User;
use App\Models\Visit;

class HomeController extends Controller
{
    public function landing()
    {
        $search = trim((string) request('q', ''));
        $format = request('format', 'all');
        $category = request('category');

        $bookQuery = Book::query()
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('ddc', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            }))
            ->when($category, fn ($query) => $query->where('category', $category));

        $ebookQuery = Ebook::query()
            ->where('is_active', true)
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            }))
            ->when($category, fn ($query) => $query->where('category', $category));

        return view('welcome', [
            'bookCount' => Book::count(),
            'ebookCount' => Ebook::where('is_active', true)->count(),
            'memberCount' => User::where('role', 'member')->count(),
            'visitCount' => Visit::whereDate('check_in_at', today())->count(),
            'loanCount' => Loan::where('status', 'borrowed')->count(),
            'books' => in_array($format, ['all', 'book'], true) ? $bookQuery->latest()->take(8)->get() : collect(),
            'ebooks' => in_array($format, ['all', 'ebook'], true) ? $ebookQuery->latest()->take(8)->get() : collect(),
            'categories' => Book::query()
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->merge(Ebook::query()->where('is_active', true)->whereNotNull('category')->distinct()->pluck('category'))
                ->filter()
                ->unique()
                ->sort()
                ->values(),
            'selectedFormat' => $format,
            'selectedCategory' => $category,
            'search' => $search,
        ]);
    }

    public function bookDetail(Book $book)
    {
        return view('catalog.book', [
            'book' => $book,
            'relatedBooks' => Book::query()
                ->whereKeyNot($book->id)
                ->where(function ($query) use ($book) {
                    $query->where('category', $book->category)
                        ->orWhere('ddc', 'like', substr((string) $book->ddc, 0, 1).'%');
                })
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }

    public function ebookDetail(Ebook $ebook)
    {
        abort_unless($ebook->is_active, 404);

        return view('catalog.ebook', [
            'ebook' => $ebook,
            'relatedEbooks' => Ebook::query()
                ->where('is_active', true)
                ->whereKeyNot($ebook->id)
                ->where('category', $ebook->category)
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }

    public function attendance()
    {
        return view('attendance.public', [
            'todayVisits' => Visit::whereDate('check_in_at', today())->count(),
            'activeVisitors' => Visit::whereDate('check_in_at', today())->whereNull('check_out_at')->count(),
            'recentVisits' => Visit::latest('check_in_at')->take(6)->get(),
        ]);
    }

    public function attendanceKiosk()
    {
        return view('attendance.kiosk', [
            'todayVisits' => Visit::whereDate('check_in_at', today())->count(),
            'activeVisitors' => Visit::whereDate('check_in_at', today())->whereNull('check_out_at')->count(),
            'recentVisits' => Visit::latest('check_in_at')->take(8)->get(),
        ]);
    }

    public function dashboard()
    {
        return view('dashboard.index', [
            'bookCount' => Book::count(),
            'availableStock' => Book::sum('stock_available'),
            'memberCount' => User::where('role', 'member')->count(),
            'todayVisits' => Visit::whereDate('check_in_at', today())->count(),
            'activeLoans' => Loan::where('status', 'borrowed')->count(),
            'overdueLoans' => Loan::where('status', 'borrowed')->whereDate('due_at', '<', today())->count(),
            'recentLoans' => Loan::with(['book', 'member'])->latest()->take(8)->get(),
        ]);
    }
}
