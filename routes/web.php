<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CirculationController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'landing'])->name('landing');
Route::get('/katalog/buku', [HomeController::class, 'bookCatalog'])->name('catalog.books.index');
Route::get('/katalog/buku/{book}', [HomeController::class, 'bookDetail'])->name('catalog.books.show');
Route::get('/katalog/ebook', [HomeController::class, 'ebookCatalog'])->name('catalog.ebooks.index');
Route::get('/katalog/ebook/{ebook}', [HomeController::class, 'ebookDetail'])->name('catalog.ebooks.show');
Route::get('/absensi-perpustakaan', [HomeController::class, 'attendanceKiosk'])->name('attendance.kiosk');
Route::post('/absensi-perpustakaan', [AttendanceController::class, 'publicStore'])->name('attendance.kiosk.store');
Route::post('/absensi-perpustakaan/register', [AttendanceController::class, 'kioskRegister'])->name('attendance.kiosk.register');
Route::get('/daftar-hadir', [HomeController::class, 'attendance'])->name('attendance.public');
Route::post('/daftar-hadir', [AttendanceController::class, 'publicStore'])->name('attendance.public.store');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/member/ebooks', [EbookController::class, 'reader'])->name('ebooks.reader');
    Route::get('/member/ebooks/{ebook}/read', [EbookController::class, 'read'])->name('ebooks.read');
    Route::get('/member/ebooks/{ebook}/download', [EbookController::class, 'download'])->name('ebooks.download');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('authors', [AuthorController::class, 'index'])->name('authors.index');
    Route::get('authors/{author}', [AuthorController::class, 'show'])->name('authors.show');
    Route::get('publishers', [PublisherController::class, 'index'])->name('publishers.index');
    Route::get('publishers/{publisher}', [PublisherController::class, 'show'])->name('publishers.show');
    Route::get('books/{book}/print-info', [BookController::class, 'printInfo'])->name('books.print-info');
    Route::get('books/{book}/print-spine', [BookController::class, 'printSpine'])->name('books.print-spine');
    Route::resource('books', BookController::class)->except('show');
    Route::resource('members', MemberController::class)->parameters(['members' => 'member'])->except('show');
    Route::resource('ebooks', EbookController::class)->except('show');
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::patch('attendance/{visit}/checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');
    Route::get('circulation', [CirculationController::class, 'index'])->name('circulation.index');
    Route::post('circulation', [CirculationController::class, 'store'])->name('circulation.store');
    Route::patch('circulation/{loan}/return', [CirculationController::class, 'return'])->name('circulation.return');
});
