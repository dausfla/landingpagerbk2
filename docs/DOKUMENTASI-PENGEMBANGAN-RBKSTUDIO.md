# 📘 DOKUMENTASI PENGEMBANGAN & SOLUSI TEKNIS
## PT. RANCANG BANGUN SEDAYA (RBK Studio & RBK Konstruksi)

> **Website**: [https://rbkstudio.id](https://rbkstudio.id)  
> **Versi Dokumen**: 1.0 (Final Release)  
> **Tanggal**: 6 Oktober 2026  
> **Penyusun**: Tim Pengembang Web & Sistem RBK Studio  

---

## 1. EXECUTIVE SUMMARY (RINGKASAN EKSEKUTIF)

Dokumen ini merupakan dokumentasi teknis dan panduan operasional menyeluruh untuk sistem website **RBK Studio & RBK Konstruksi** ([rbkstudio.id](https://rbkstudio.id)). Dokumen ini mencakup seluruh perbaikan visual, optimasi tampilan mobile (iOS/Android), perbaikan backend & manajemen database, serta optimasi SEO dan pencarian Google dari awal hingga akhir pengembangan.

Website dibangun dengan pendekatan kustom (*high performance*), tanpa dependensi berat, menggunakan **PHP 7.4 FPM, Web Server Nginx, Database MySQL, Vanilla CSS Modern (Design Tokens), dan ES6 JavaScript**.

---

## 2. DAFTAR PERBAIKAN & PENYEMPURNAAN FITUR (AWAL SAMPAI AKHIR)

### 🎨 A. Branding Identity & Visual Layout
1. **Pemasangan Favicon Resmi Logo Emas PT. RANCANG BANGUN SEDAYA**:
   - Dibuatkan set paket favicon lengkap sesuai standar Google Crawler & Apple iOS:
     - `favicon.ico` (Multi-size ICO untuk browser desktop)
     - `favicon.png` (512x512 High-Res PNG)
     - `apple-touch-icon.png` (180x180 untuk iOS Home Screen)
     - `android-chrome-192x192.png` & `android-chrome-512x512.png` (untuk Android)
     - `favicon-32x32.png` & `favicon-16x16.png`
   - Terintegrasi otomatis pada `<head>` HTML sehingga Google Search dapat menampilkan Logo Emas RBK di samping URL `rbkstudio.id`.

2. **Pembaruan Visual Hero & Featured Project**:
   - Gambar Hero utama disesuaikan dengan visual arsitektur tinggi (`la-bella.jpg`).
   - Fitur unggulan *Before/After Interactive Slider* menggunakan sampel proyek nyata H House Pasirmulya (`h-house.jpg` & `h-house-before.jpg`).

3. **Redesain Footer Komprehensif**:
   - Penambahan Link Google Maps Lokasi Kantor Resmi: `https://share.google/U2HIjL6kC37V8KlSA`.
   - Pemasangan Icon Sosial Media Vector SVG (Instagram, YouTube, TikTok, Facebook).
   - Penyederhanaan akses admin menjadi satu link aman ke portal login (`/admin/login`).

---

### 📱 B. Optimasi Tampilan Device Mobile (iOS & Android)
1. **Section S7 (Desain Rumah Tropis Bogor)**:
   - **Masalah**: Pada layar HP, layout grid 2 kolom dipaksa berdampingan (`1fr 1fr`), menyebabkan teks terhimpit tegak dan gambar rumah terpotong sempit.
   - **Solusi**: Mengganti inline style dengan class `.tropical-grid`. Pada tampilan HP (`@media (max-width: 768px)`), layout otomatis menjadi **1 kolom vertikal full-width** (teks di atas, gambar rumah utuh di bawah).

2. **Section S8 (Badge Filter Kategori Portofolio)**:
   - **Masalah**: Tombol badge kategori ("Semua Proyek", "Rumah", "Kost", "Ruko & Komersial", dll.) bertumpuk menjadi 3 baris yang sempit dan berantakan di layar HP.
   - **Solusi**: Mengubah `.chip-group` pada layar HP menjadi **Horizontal Scroll Pill Bar** yang lapang. Pengguna dapat menggeser kategori menyamping (*touch swipe*) layaknya aplikasi mobile native tanpa ada baris bertumpuk yang sempit.

---

### ⚡ C. Backend, Database & Manajemen Portofolio
1. **Penanganan Upload Foto Berukuran Besar (No Loading Lama)**:
   - Mengatasi batas unggah Nginx dengan konfigurasi `client_max_body_size 100M`.
   - Menambahkan fitur **Kompresi Gambar Otomatis & Auto-Resize** berbasis GD Graphics Library pada `PortfolioController.php`. Foto berukuran besar dari HP/Kamera (misal: 10MB+) secara otomatis diperkecil ke dimensi max 1920px dan dikompresi ke kualitas optimal tanpa mengorbankan ketajaman visual.

2. **Pencegahan Error Duplikat (Auto Unique Slug Generator & Exception Safety)**:
   - **Masalah**: Ketika admin menambah portofolio dengan nama yang sama atau yang pernah dihapus sebelumnya, sistem melempar error `PDOException 1062 Duplicate entry`.
   - **Solusi**: 
     - Dibuatkan fungsi `makeUniqueSlug()` yang memeriksa ketersediaan slug URL secara *table-wide* (termasuk data terhapus).
     - Jika slug sudah pernah ada di data terhapus, sistem otomatis mengubah nama slug data terhapus tersebut agar slug utama bebas dipakai kembali.
     - Jika slug ada di data aktif, sistem otomatis memberikan akhiran penomoran (`-1`, `-2`, dst.).
     - Dilengkapi proteksi `try...catch` yang menampilkan notifikasi merah ramah pengguna jika terjadi kendala pada formulir.

---

### 🔍 D. Optimasi SEO & Google Search Engine
1. **Judul Halaman Google (Meta Title)**:
   - Diperbarui secara resmi menjadi:
     `"Jasa Desain Arsitek & Kontraktor Rumah Bogor, Jasa Arsitek Jabodetabek | RBK Studio"`
2. **Meta Description & Open Graph**:
   - Pemasangan `og:title`, `og:description`, dan `og:image` (`https://rbkstudio.id/favicon.png`) untuk kerapihan tampilan saat link dibagikan di WhatsApp, Meta Ads, Facebook, maupun Google.
3. **Pengajuan Pengindeksan Google Search Console**:
   - Pengajuan perayapan ulang (*Request Indexing*) telah dilakukan melalui Google Search Console untuk mempercepat pembaruan Judul & Favicon Emas di halaman pencarian Google.

---

## 3. PANDUAN OPERASIONAL & MAINTENANCE

### 🔐 A. Akses Admin Dashboard
- **URL Login**: `https://rbkstudio.id/admin/login`
- **Fungsi Utama**:
  1. **Portofolio Proyek**: Tambah, edit, hapus proyek, serta upload foto Sebelum & Sesudah (*Before/After*).
  2. **Pengaturan Website (Settings)**: Mengubah Meta Title, Meta Description, pesan WhatsApp otomatis, dan data kontak.

### 🚀 B. Perintah Update / Deploy 1 Baris di Server VPS
Jika terjadi perubahan kode di masa mendatang, jalankan perintah berikut di terminal VPS:

```bash
cd /var/www/rbkstudio && git pull origin main && php -r "require 'vendor/autoload.php'; App\Core\Cache::clearAll();" 2>/dev/null || true && systemctl restart nginx php7.4-fpm
```

---

## 4. PENUTUP

Dengan diselesaikannya seluruh rangkaian perbaikan ini, website **RBK Studio** ([rbkstudio.id](https://rbkstudio.id)) kini memiliki tampilan yang responsif, modern, dan cepat di seluruh perangkat (Desktop, iPhone, Android), terproteksi dari error teknis, serta memiliki identitas SEO yang kuat di pencarian Google.

*Dokumen ini dibuat untuk disimpan sebagai arsip pegangan internal RBK Studio dan dapat dibagikan kepada klien.*
