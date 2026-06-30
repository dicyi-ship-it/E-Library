<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CirculationController extends Controller
{
    public function index()
    {
        return view('circulation.index', [
            'loans' => Loan::with(['book', 'member'])->latest()->paginate(15),
            'books' => Book::where('stock_available', '>', 0)->orderBy('title')->get(),
            'members' => User::whereIn('role', ['member', 'staff'])->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'member_id' => ['required', 'exists:users,id'],
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after_or_equal:borrowed_at'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $book = Book::lockForUpdate()->findOrFail($data['book_id']);

            if ($book->stock_available < 1) {
                abort(422, 'Stok buku tidak tersedia.');
            }

            $book->decrement('stock_available');

            Loan::create($data + [
                'loan_code' => 'LOAN-'.now()->format('YmdHis').'-'.$book->id,
                'status' => 'borrowed',
            ]);
        });

        return redirect()->route('circulation.index')->with('status', 'Peminjaman berhasil dicatat.');
    }

    public function return(Loan $loan)
    {
        if ($loan->status === 'returned') {
            return redirect()->route('circulation.index')->with('status', 'Buku ini sudah dikembalikan.');
        }

        DB::transaction(function () use ($loan) {
            $loan->load('book');
            $overdueDays = max(0, now()->startOfDay()->diffInDays($loan->due_at, false) * -1);

            $loan->update([
                'returned_at' => today(),
                'status' => 'returned',
                'fine_amount' => $overdueDays * 1000,
            ]);

            $loan->book->increment('stock_available');
        });

        return redirect()->route('circulation.index')->with('status', 'Pengembalian berhasil dicatat.');
    }
}
