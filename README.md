# E-Library

Aplikasi perpustakaan open source berbasis Laravel 12 untuk sekolah, kampus, dan institusi pendidikan. Identitas aplikasi bersifat white-label: nama institusi, nama perpustakaan, nama aplikasi, dan teks logo dapat diubah dari menu admin `Pengaturan`.

Default instalasi contoh memakai `STTI NIIT I-Tech`, tetapi dapat diganti sesuai institusi pengguna.

Fitur utama:

- CRUD master buku lengkap dengan DDC, nomor panggil, ISBN, rak, lokasi, cover, dan stok.
- Keanggotaan universitas untuk mahasiswa, dosen, tenaga kependidikan, dan peneliti.
- Kehadiran pengunjung perpustakaan.
- Ebook untuk member terdaftar, dengan opsi file lokal atau URL eksternal.
- Sirkulasi peminjaman, pengembalian, jatuh tempo, denda, dan stok otomatis.
- Indeks penulis dan penerbit beserta koleksi bukunya.
- Pengaturan aplikasi untuk kebutuhan white-label sekolah/kampus.
- Landing page publik dan kode sederhana supaya mudah dikembangkan ulang.

## Menjalankan Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Sesuaikan database di `.env`. Contoh akun awal dari seeder:

- Admin: `admin@elibrary.test` / `password`
- Member: `member@elibrary.test` / `password`

## Lisensi

MIT. Silakan pakai, modifikasi, dan rilis ulang dengan tetap menyertakan atribusi yang sesuai.
