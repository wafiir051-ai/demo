# Goding Hospitality — Website & Admin Management System

*Goding — Build Your Digital Presence with Goding.* · https://www.godingofficial.com/ · @goding.id

Website hospitality (villa, hotel, guest house, homestay, venue) + dashboard admin. Laravel + SQLite lokal + Blade. Tidak butuh layanan cloud.

## Instalasi (semua dari terminal)

Prasyarat: PHP 8.2+ (dengan `pdo_sqlite`, `mbstring`, `xml`), Composer.

```bash
# macOS
brew install php composer

# dari folder yang berisi file ini:
bash setup.sh goding-hospitality
cd goding-hospitality
php artisan serve
```

Skrip membuat proyek Laravel baru, menyalin seluruh file aplikasi, membuat `database/database.sqlite`, mengatur `.env`, menjalankan migration + seeder, dan `storage:link`.

Instalasi manual: `composer create-project laravel/laravel app`, salin isi folder ini ke dalamnya (timpa), lalu `touch database/database.sqlite && php artisan migrate:fresh --seed && php artisan storage:link`.

## Akun demo
| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@goding.test | password |
| Admin | admin@goding.test | password |
| Staff | staff@goding.test | password |

**Ganti password sebelum dipakai sungguhan** (Pengaturan Akun / menu Pengguna).

## Hak akses
- **Super Admin**: semua menu, termasuk Pengguna dan Role.
- **Admin**: konten, pengaturan bisnis, reservasi, pesan, log.
- **Staff**: dashboard, reservasi, pesan, akun sendiri.

Dipaksa di backend lewat middleware `RoleMiddleware` (bukan sekadar menyembunyikan menu).

## Mengganti bisnis (tanpa ubah kode)
Admin → **Pengaturan Bisnis**: nama, logo, favicon, hero, warna, kontak, WhatsApp, alamat, peta, sosial media, SEO. Langsung tampil di website. Matikan label demo dengan menghapus centang "mode demo".

Hapus seluruh data demo: `php artisan goding:clear-demo` (akun pengguna dan migration tidak disentuh).

## Fitur
Public: beranda, akomodasi + detail, fasilitas, paket & acara, galeri (filter + lightbox), tentang, kontak, halaman dinamis `/p/{slug}`, formulir reservasi (kode unik, status `pending`), tombol WhatsApp, 404.
Admin: dashboard (data nyata + grafik dari DB), pengaturan bisnis, CRUD akomodasi/fasilitas/galeri/paket/halaman/pengguna, reservasi (cari, filter status & tanggal, detail, ubah status, catatan internal), pesan masuk, activity log, pengaturan akun.

## Catatan penting (kejujuran fitur)
- **Permintaan reservasi bukan konfirmasi.** Saat admin mengubah status ke *Dikonfirmasi*, sistem mengecek bentrok tanggal dengan reservasi *terkonfirmasi* lain pada akomodasi yang sama (dalam transaksi DB). Belum ada kalender/inventaris kamar yang menjamin ketersediaan, dan formulir publik tidak memblokir tanggal.
- **Email belum dikirim.** Tidak ada layanan email terkonfigurasi; sistem tidak mengklaim email terkirim.
- **Testimoni tidak disertakan** (menunggu data terverifikasi).
- Gambar yang belum diunggah memakai placeholder SVG; semua bisa diganti dari dashboard.
- Styling memakai CSS sendiri (`public/css`), tanpa build step Node.
- Kode ini ditulis tanpa dapat dijalankan di lingkungan penyusun. Jalankan `php artisan test` setelah instalasi untuk memverifikasi alur utama, dan laporkan error bila ada.

## Keamanan
Login server-side, hash password, CSRF, validasi backend, throttle login/form, honeypot anti-spam, validasi upload (jpg/png/webp, maks 3 MB, tanpa SVG), output di-escape (konten halaman memakai Markdown dengan HTML mentah dinonaktifkan), log tanpa password. Sebelum produksi: ganti password demo, set `APP_ENV=production`, `APP_DEBUG=false`, pakai HTTPS, dan tinjau ulang.

## Struktur
`app/Http/Controllers/Admin/CrudController.php` = CRUD generik berbasis konfigurasi; tiap resource hanya mendefinisikan `cfg()`. Menambah resource baru = satu controller + satu baris `$crud(...)` di `routes/web.php`.
