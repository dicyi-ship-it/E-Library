# E-Library

E-Library adalah aplikasi perpustakaan open source berbasis Laravel untuk sekolah, kampus, lembaga kursus, dan institusi pendidikan yang membutuhkan sistem katalog, absensi pengunjung, ebook, dan sirkulasi peminjaman dalam satu aplikasi.

Project ini dibuat dengan konsep white-label. Artinya nama institusi, nama perpustakaan, nama aplikasi, dan teks logo dapat diganti langsung dari menu admin tanpa perlu mengubah kode. Default demo memakai `STTI NIIT I-Tech`, tetapi dapat diganti menjadi nama sekolah atau kampus mana pun.

## Kenapa E-Library?

Banyak sekolah dan kampus membutuhkan aplikasi perpustakaan yang sederhana, bisa dipasang sendiri, dan tidak terkunci pada vendor tertentu. E-Library berusaha menjadi fondasi yang rapi untuk kebutuhan itu:

- Bisa dipakai sebagai katalog buku fisik.
- Bisa mencatat kunjungan perpustakaan melalui halaman kiosk.
- Bisa mengelola ebook/PDF.
- Bisa mencatat peminjaman dan pengembalian.
- Bisa disesuaikan untuk identitas institusi masing-masing.
- Kodenya cukup sederhana untuk dikembangkan ulang oleh tim IT sekolah/kampus.

## Fitur Utama

### Landing Page Publik

- Tampilan modern putih-biru.
- Katalog buku dan ebook terbaru.
- Pencarian koleksi berdasarkan judul, penulis, penerbit, ISBN, DDC, atau kategori.
- Kartu buku/ebook dapat diklik untuk membuka halaman informasi detail.

### Katalog Buku

- CRUD buku fisik.
- Data bibliografi: judul, subjudul, ISBN, DDC, nomor panggil, penulis, penerbit, tahun, edisi, bahasa, kategori, rak, lokasi, deskripsi, cover, stok.
- Nomor panggil dapat dibuat otomatis dari DDC, kode penulis, dan tahun.
- Cetak info buku A4.
- Cetak label nomor punggung.
- Detail buku publik yang bisa dibuka dari admin, landing, penulis, dan penerbit.

### Indeks Penulis dan Penerbit

- Penulis dan penerbit tidak hanya disimpan sebagai teks, tetapi juga diindeks.
- Saat CRUD buku, admin bisa memilih penulis/penerbit lama atau mengetik nama baru.
- Nama baru otomatis masuk ke indeks.
- Halaman penulis menampilkan daftar buku terkait.
- Halaman penerbit menampilkan daftar buku terkait.

### Ebook

- CRUD ebook.
- Mendukung file lokal atau URL eksternal.
- Ebook aktif tampil di landing page.
- Member dapat membaca atau mengunduh ebook.
- Statistik jumlah unduhan.

### Absensi Pengunjung Perpustakaan

- Halaman kiosk khusus untuk PC perpustakaan: `/absensi-perpustakaan`.
- Check-in dengan input nomor induk seperti NIM, NIS, NIDN, NUPTK, nomor anggota, atau nomor unik lain.
- Dukungan scan QR berbasis browser jika perangkat dan browser mendukung `BarcodeDetector`.
- Registrasi cepat pengunjung baru langsung dari halaman kiosk.
- Setelah registrasi, kehadiran langsung tercatat.
- Admin tetap memiliki halaman rekap kehadiran.

### Sirkulasi

- Peminjaman buku.
- Pengembalian buku.
- Stok otomatis berkurang saat dipinjam dan bertambah saat dikembalikan.
- Tanggal pinjam dan jatuh tempo.
- Denda sederhana untuk keterlambatan.
- Input buku dan anggota pada sirkulasi dapat diketik untuk mencari data secara interaktif.

### Keanggotaan

- CRUD anggota.
- Data anggota: nama, email, nomor anggota, nomor identitas, telepon, fakultas/unit, departemen, program studi, level, status.
- Role admin, member, dan staff.

### White-Label / Pengaturan Aplikasi

Admin dapat membuka:

```text
/admin/settings
```

Lalu mengubah:

- Nama aplikasi
- Nama institusi
- Nama perpustakaan
- Teks logo singkat

Pengaturan ini digunakan di navbar, landing page, absensi, print buku, dan beberapa tampilan publik lain.

## Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL/MariaDB atau database lain yang didukung Laravel
- Vite
- Tailwind CSS 4
- JavaScript vanilla untuk interaksi ringan

## Kebutuhan Sistem

Pastikan server/lokal memiliki:

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- Database MySQL/MariaDB
- Extension PHP umum untuk Laravel seperti `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, dan `fileinfo`

## Instalasi Lokal

Clone repository:

```bash
git clone https://github.com/dicyi-ship-it/E-Library.git
cd E-Library
```

Install dependency PHP:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

Salin file environment:

```bash
cp .env.example .env
```

Atur koneksi database di `.env`, contoh:

```env
APP_NAME="E-Library"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_library
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key:

```bash
php artisan key:generate
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

Buat symbolic link storage:

```bash
php artisan storage:link
```

Build asset frontend:

```bash
npm run build
```

Jalankan server lokal:

```bash
php artisan serve
```

Buka aplikasi:

```text
http://127.0.0.1:8000
```

## Akun Demo

Seeder menyediakan akun awal:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@elibrary.test` | `password` |
| Member | `member@elibrary.test` | `password` |

Setelah login admin, buka menu `Pengaturan` untuk mengganti identitas institusi.

## Alur Penggunaan Singkat

1. Login sebagai admin.
2. Buka `Pengaturan` dan sesuaikan nama institusi.
3. Tambahkan anggota atau izinkan registrasi dari kiosk absensi.
4. Tambahkan penulis/penerbit saat input buku.
5. Tambahkan buku fisik beserta stok dan lokasi rak.
6. Tambahkan ebook jika dibutuhkan.
7. Gunakan halaman `/absensi-perpustakaan` di PC perpustakaan.
8. Gunakan menu `Sirkulasi` untuk peminjaman dan pengembalian.

## Struktur Modul

```text
app/Http/Controllers
  AttendanceController.php   Absensi dan registrasi kiosk
  AuthorController.php       Indeks penulis
  BookController.php         Master buku dan cetak
  CirculationController.php  Peminjaman dan pengembalian
  EbookController.php        Master ebook dan reader
  HomeController.php         Landing, katalog, dashboard
  MemberController.php       Master anggota
  PublisherController.php    Indeks penerbit
  SettingController.php      Pengaturan white-label

app/Models
  AppSetting.php
  Author.php
  Book.php
  Ebook.php
  Loan.php
  Publisher.php
  User.php
  Visit.php
```

## Halaman Penting

| Halaman | Fungsi |
| --- | --- |
| `/` | Landing page publik |
| `/katalog/buku` | Katalog buku fisik |
| `/katalog/buku/{id}` | Informasi detail buku |
| `/katalog/ebook` | Katalog ebook |
| `/katalog/ebook/{id}` | Informasi detail ebook |
| `/absensi-perpustakaan` | Kiosk daftar hadir |
| `/admin/dashboard` | Dashboard admin |
| `/admin/books` | Master buku |
| `/admin/authors` | Indeks penulis |
| `/admin/publishers` | Indeks penerbit |
| `/admin/ebooks` | Master ebook |
| `/admin/members` | Master anggota |
| `/admin/attendance` | Rekap kehadiran |
| `/admin/circulation` | Sirkulasi peminjaman |
| `/admin/settings` | Pengaturan white-label |

## Testing

Jalankan test:

```bash
php artisan test
```

Build frontend:

```bash
npm run build
```

## Catatan Produksi

Sebelum dipakai di server produksi:

- Ubah `APP_ENV=production`.
- Ubah `APP_DEBUG=false`.
- Gunakan password admin yang kuat.
- Atur database produksi.
- Jalankan `php artisan migrate --force`.
- Jalankan `npm run build`.
- Pastikan folder `storage` dan `bootstrap/cache` writable.
- Jalankan `php artisan storage:link`.
- Atur web server ke folder `public`.

## Roadmap Ide Pengembangan

Beberapa fitur yang bisa dikembangkan berikutnya:

- Import buku dari Excel/CSV.
- Barcode/QR untuk buku dan kartu anggota.
- Reservasi buku.
- Laporan statistik bulanan.
- Export data peminjaman dan kunjungan.
- Multi-cabang perpustakaan.
- Hak akses role yang lebih granular.
- Integrasi SSO kampus/sekolah.
- Template kartu anggota.

## Kontribusi

Kontribusi sangat terbuka. Silakan fork repository ini, buat branch fitur/perbaikan, lalu ajukan pull request.

Contoh alur kontribusi:

```bash
git checkout -b fitur/nama-fitur
git commit -m "Tambah nama fitur"
git push origin fitur/nama-fitur
```

Lalu buat pull request dari GitHub.

## Lisensi

MIT License. Silakan gunakan, modifikasi, dan rilis ulang aplikasi ini untuk kebutuhan sekolah, kampus, komunitas, atau institusi lain dengan tetap menyertakan atribusi yang sesuai.

## Dukungan

Jika project ini bermanfaat, bantu dengan:

- Memberi star di GitHub.
- Membagikan ke sekolah/kampus yang membutuhkan.
- Mengirim issue untuk bug atau ide fitur.
- Mengirim pull request untuk perbaikan.
