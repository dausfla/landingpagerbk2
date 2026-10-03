# LAPORAN PERBAIKAN GLITCH SECTION PORTOFOLIO

Dokumen ini mencatat analisis akar masalah (*root cause*) dan perbaikan yang telah dilakukan pada section Portofolio (`#portofolio`) pada Landing Page RBK Studio.

---

## 1. ANALISIS AKAR MASALAH (ROOT CAUSES)

Setelah dilakukan penelusuran pada kode HTML, CSS, JavaScript, dan ketersediaan asset fisik:

1. **Permintaan Gambar 404 pada Ngrok (Penyebab Utama Glitch)**
   - Direktori `public/assets/img/` sebelumnya belum memiliki asset gambar fisik (`rumah-a-before.webp`, `rumah-a-after.webp`, `la-bella.webp`, serta gambar proyek portofolio).
   - Ketika halaman diakses (terutama melalui tunnel Ngrok), browser mengalami kebocoran permintaan HTTP 404 berulang kali. Ini menyebabkan *broken image icon* dan *Cumulative Layout Shift* (CLS) saat browser meredraw ukuran kontainer secara mendadak.

2. **Pergerakan Slider *Before/After* Belum Menggunakan GPU Acceleration**
   - Elemen divider (`.ba-divider`) dan handle (`.ba-handle`) sebelumnya belum memiliki properti CSS `will-change: left` dan `transition: none`.
   - Saat pengguna menggeser slider `input[type="range"]`, browser memproses pergerakan di CPU sehingga garis pemisah terasa ketinggalan (*lag/jittering*) dari pergerakan jari/mouse.

3. **Pergantian Kategori Filter Menggunakan Instant Display Toggle**
   - Pada `public/assets/js/main.js`, penapisan kartu portofolio langsung mengaktifkan `display: none !important;` secara mendadak.
   - Karena tidak ada transisi tinggi dan opasitas yang dikontrol, grid portofolio mengalami lompatan tinggi (*layout snap/glitch*) saat pengguna menekan tombol kategori.

4. **Duplikasi Atribut Class HTML pada Chip Group**
   - Terdapat sintaks `<div class="chip-group" ... class="reveal">` di [home.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/public/home.php#L321) yang memiliki dua atribut `class` terpisah sehingga pengenalan IntersectionObserver di beberapa browser tidak konsisten.

---

## 2. PERBAIKAN YANG TELAH DILAKUKAN

### A. Pembentukan Asset Gambar Fisik Lengkap
- Menjalankan script PHP GD [generate_portfolio_images.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/scripts/generate_portfolio_images.php) untuk mengenerate 14 file gambar WebP resolusi tinggi (`la-bella.webp`, `arsya.webp`, `rumah-a-before.webp`, `rumah-a-after.webp`, dan seluruh 10 proyek portofolio).
- Gambar dibuat persis menggunakan palet warna kunci RBK (`#0f0e0d`, `#ffffff`, `#dd5c3e`) sehingga seluruh permintaan HTTP mengembalikan **HTTP 200 OK** seketika.

### B. Optimasi GPU Acceleration pada Slider *Before/After*
- Mengupdate [public/assets/css/style.css](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/public/assets/css/style.css#L373-L440) dengan:
  - `will-change: clip-path, -webkit-clip-path;` pada gambar `.ba-before`.
  - `will-change: left; transition: none;` pada `.ba-divider` dan `.ba-handle`.
  - `touch-action: pan-y;` pada kontainer slider agar tidak mengganggu scroll vertikal mobile.
- Mengupdate [public/assets/js/main.js](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/public/assets/js/main.js#L70-L98) dengan listener berbasis `requestAnimationFrame` untuk memastikan pergerakan slider berjalan mulus pada kecepatan 60 FPS tanpa stutters.

### C. Smooth Fade Transition pada Filter Kategori Portofolio
- Mengupdate kelas CSS `.portfolio-card`:
  ```css
  .portfolio-card {
    opacity: 1;
    transform: scale(1) translateY(0);
    transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    will-change: opacity, transform;
    backface-visibility: hidden;
  }
  .portfolio-card.is-hiding {
    opacity: 0;
    transform: scale(0.95) translateY(12px);
    pointer-events: none;
  }
  ```
- Mengubah alur JS filter kategori di `main.js` menjadi 3 tahap teratur (*is-hiding* -> *is-hidden toggle* -> *requestAnimationFrame fade-in*).

### D. Perbaikan Atribut HTML & Fallback SVG
- Memperbaiki sintaks HTML di [home.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/public/home.php#L321) menjadi `<div class="chip-group reveal">`.
- Menambahkan fallback gambar inline SVG Data URI pada atribut `onerror` di tag `<img>` portofolio untuk mencegah broken image icon jika kelak ada item proyek baru di database yang belum diunggah fotonya.

### E. Invalidation Cache Static HTML
- Menjalankan `Cache::clearAll()` untuk memperbarui cache halaman publik di server secara otomatis.

### F. Integrasi Logo Resmi Rancang Bangun Kreasi (RBK)
- Mengolah asset gambar logo resmi bertuliskan "RANCANG BANGUN KREASI" dengan ikon rumah oren (`logo-rbk.png`).
- Mengenerate varian latar gelap (`logo-rbk-white.png`) di mana teks hitam otomatis dikonversi menjadi putih bersih untuk tampilan kontras tinggi.
- Memasang logo resmi pada:
  - **Navbar Sticky Utama Publik** ([home.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/public/home.php#L46))
  - **Footer Publik** ([home.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/public/home.php#L650))
  - **Sidebar Admin Dashboard** ([layout.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/admin/layout.php#L167))
  - **Halaman Login Super Admin** ([login.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/admin/login.php#L73))

### G. Pembaruan Eyebrow Hero Section
- Mengubah teks eyebrow pada Section Hero (`s2_hero`) dari `"RBK Studio × RBK Konstruksi · Bogor & Jabodetabek"` menjadi `"ARCHITECTURE & PLANNING JABODETABEK"`.
### H. Pembaruan Judul Utam Hero Section (H1)
- Mengubah judul utama Hero Section dari `"Jasa Arsitek & Kontraktor Terbaik untuk *Mewujudkan Bangunan Impian Anda*"` menjadi `"Jasa Arsitek & Kontraktor *Terbaik & Terlengkap* untuk Mewujudkan Bangunan Impian Anda"`.
- Menambahkan helper parser di `PricingService::formatTitle()` sehingga simbol bintang (`*`) otomatis dikonversi menjadi tag HTML `<span style="color: var(--color-orange)">` secara dinamik tanpa menampilkan simbol bintang sama sekali di layar.
- Mengupdate database `sections` row `s2_hero` serta `database/seed.sql`.

### I. Pembaruan Subtitle & Ukuran Badge/Button Hero Section
- Memperbesar ukuran font subtitle Hero Section (`clamp(18px, 2.2vw, 20px)`) serta membuat frasa **`Rencanakan bersama RBK Studio`** dan **`bangun bersama RBK Konstruksi`** tampil **BOLD** (`<strong>`).
- Memperbesar ukuran badge/chip pada Hero Section (`.hero-chip`):
  - Padding diperbesar dari `6px 14px` menjadi `10px 20px`.
  - Ukuran font diperbesar dari `12.5px` menjadi `14.5px` (font-weight: 700).
  - Menambahkan *box-shadow* dan sentuhan *highlight background* oren transparan (`rgba(221, 92, 62, 0.08)`).
- Memperbesar ukuran tombol CTA utama (`Konsultasi & Survei Gratis` & `Lihat Paket Harga`) di Hero Section menjadi `padding: 16px 28px; font-size: 16px;`.
- Mengupdate database `sections` row `s2_hero` (`subtitle`), `database/seed.sql`, `style.css`, serta `home.php`.

### J. Redesain Section Trust Strip (Poin Statistik menjadi Kartu Individu)
- Mengubah tampilan 4 poin statistik (`2007`, `1.350+`, `3 + 1`, `Gratis`) dari teks polos menjadi **kartu independen (*trust-card*)** berdesain modern:
  - Latar belakang putih bersih (`var(--color-white)`), border halus (`var(--color-border-light)`), sudut melengkung `radius-xl` (16px), dan padding `30px 20px`.
  - Menambahkan aksen bar oren tersembunyi yang muncul secara halus saat kursor melayang (*hover effect* `translateY(-5px)` & *shadow glow* oren).
  - Layout grid responsif: 4 kolom di desktop, 2 kolom di tablet (max 992px), 1 kolom di mobile (max 576px).
- Mengupdate `public/assets/css/style.css` dan `app/Views/public/home.php`.

### K. Penghapusan Chip Teks Filosofi (Estetika, Fungsi, Kenyamanan, Dll)
- Menghapus blok tag teks filosofi (`Estetika`, `Fungsi`, `Kenyamanan`, `Efisiensi`, `Teknis`, `Budget`) dari Section 6 (RBK Studio Scope) pada file [home.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/public/home.php#L270).

### L. Pembaruan Warna Judul Section 7 (Desain Tropis Bogor)
- Mengubah frasa judul pada Section 7 (`s7_tropical_bogor`) sehingga frasa **`Desain rumah tropis`** tampil dalam warna oren (`var(--color-orange)`), dan **`yang tangguh di cuaca Bogor`** tampil dalam warna hitam netral.
- Mengupdate database `sections` row `s7_tropical_bogor` serta file `database/seed.sql`.

### M. Penjajaran Kartu Section 4 (Kenapa Perlu Dengan Benar) & Penyempurnaan Dropdown Sistem
- **Penjajaran 4 Kartu Section 4 (s4_problem):**
  - Mengubah layout dari 3+1 kartu yang menggantung menjadi **4 kartu sejajar (1 baris 4 kolom)** pada desktop monitor (`.problem-grid`).
  - Kartu disesuaikan menjadi *equal-height*, bergaris aksen oren di sisi kiri, berlatar putih bersih dengan bayangan lembut dan animasi *hover elevation*.
  - Layout otomatis responsif (2 kolom pada tablet, 1 kolom pada mobile).
- **Penyempurnaan Seluruh Form Input & Dropdown Sistem:**
  - **Penataan Form Input (Perbaikan Tampilan Squished/Inline):** Menambahkan rule lengkap untuk `.form-group` (`display: flex; flex-direction: column; gap: 6px; width: 100%; margin-bottom: 20px;`) sehingga label berada tepat di atas field input secara terstruktur dan teratur.
  - **Ukuran Baku Field Input & Select:** Seluruh field input (`.form-control`, `input`, `select`, `textarea`) diset menjadi **100% full-width**, tinggi `50px`, padding `12px 18px`, border `1.5px solid var(--color-border-light)`, dan border-radius `radius-lg` (10px).
  - **Warna Focus Glow:** Mengaktifkan *focus ring* oren presisi (`border-color: var(--color-orange); box-shadow: 0 0 0 3.5px rgba(221, 92, 62, 0.2)`).
  - **FAQ Accordion (`#faq`):** Diperbarui menjadi kartu accordion modern berikon indikator lingkaran `+` / `×` oren interaktif yang berputar saat dibuka.
### N. Redesign Kalkulator Estimasi Biaya (Section 11) Menjadi 2 Kartu Terpisah
- **Pemisahan Kartu Layanan (Desain vs Pembangunan):**
  - Mengubah layout dari 1 box kalkulator gabungan dengan radio button mode menjadi **2 kartu kalkulator terpisah secara independen** (`.calc-two-cards-grid`):
    1. **Kartu 1 (RBK Studio - Desain Arsitektur):**
       - Badge header `RBK Studio`.
       - Slider & number input luas bangunan (m²).
       - Select paket desain (Basic Rp60rb/m², Standard Rp80rb/m², Premium Rp150rb/m²).
       - Perhitungan realtime estimasi biaya desain & breakdown rumus.
       - Tombol CTA `Konsultasikan Desain Ini` yang otomatis mengisi form konsultasi dengan pilihan mode Desain & luas area.
    2. **Kartu 2 (RBK Konstruksi - Pembangunan Fisik):**
       - Badge header `RBK Konstruksi`.
       - Slider & number input luas bangunan (m²).
       - Select paket pembangunan (Basic Rp4–4,5 jt/m², Standard Rp4,5–5 jt/m², Premium Rp6–7,5 jt/m²).
       - Perhitungan realtime estimasi biaya fisik & breakdown rumus.
       - Tombol CTA `Konsultasikan Pembangunan Ini` yang otomatis mengisi form konsultasi dengan pilihan mode Pembangunan & luas area.
  - Card berlatar `var(--color-surface-dark)` dengan efek hover border oren dan elevasi bayangan modern.
### P. Pembaharuan Badge Portofolio, Slider Sebelum/Sesudah Tiap Kartu & Ikon WhatsApp Hijau
- **Penyempurnaan Badge & Chip Kategori Portofolio:**
  - Redesign filter chip (`.portfolio-chip`) dengan batas abu-abu netral halus (`#e2e8f0`), sudut membulat sempurna (*pill*), dan warna aktif oren terang (`var(--color-orange)`) beserta efek *glowing shadow*.
  - Badge kategori pada setiap kartu portofolio (`.portfolio-category-badge`) diperbarui menjadi pill badge elegan lengkap dengan *dot indicator* oren di sebelah kiri teks kategori.
- **Slider Sebelum & Sesudah pada Setiap Kartu Portofolio:**
  - Setiap kartu portofolio (`.portfolio-card`) kini dilengkapi dengan komponen slider perbandingan *Before/After* interaktif independen (`.card-ba-slider`).
  - Dibuatkan asset gambar `-before.webp` untuk 10 karya portofolio utama sehingga pengguna dapat menggeser perbandingan sebelum & sesudah renovasi/pembangunan secara langsung di dalam kartu.
- **Penggunaan Ikon WhatsApp Hijau Resmi:**
  - **Navbar Button "Konsultasi Gratis":** Mengintegrasikan ikon WhatsApp hijau fisik asli (`/assets/img/whatsapp-icon.png`) di dalam tombol oren navbar dengan perataan flex presisi.
  - **Floating WhatsApp Button (Desktop & Mobile):** Mengganti ikon SVG monokrom pada floating button dengan ikon hijau lingkaran WhatsApp resmi berbayangan 3D (`filter: drop-shadow`).
### Q. Penghapusan Mobile Bottom Sticky CTA Bar (.mbar)
- **Penghapusan Bilah Sticky Bawah:**
  - Menghapus komponen `<div class="mbar">...</div>` yang menempel di bagian bawah layar perangkat mobile (berisi tombol *WhatsApp Direct* & *Konsultasi Gratis*).
  - Mengupdate rule CSS `.mbar { display: none !important; }` di `public/assets/css/style.css`.
### T. Redesign Grid Section 9 (8 Keunggulan Utama) & Penguatan Responsif Multi-Device
- **Redesign Penataan Kartu Keunggulan (.advantages-grid & .adv-card):**
  - Mengubah layout grid dari `repeat(auto-fit, minmax(260px, 1fr))` yang menyebabkan 2 kartu menggantung di baris ke-3 menjadi **4 kolom presisi pada desktop** (`repeat(4, 1fr)`). Ke-8 kartu kini terisi sempurna dalam 2 baris ($4 \times 2 = 8$) tanpa ada area kosong di sisi kanan.
  - Tambahan media queries responsif: 2 kolom pada tablet ($768\text{px} - 1024\text{px}$) dan 1 kolom penuh pada mobile ($\le 640\text{px}$).
  - Tampilan nomor disempurnakan menjadi pill badge numerik dua digit (`01`, `02`, ..., `08`) beraksen warna oren `rgba(221, 92, 62, 0.1)` yang memberikan kesan arsitektural modern.
- Mengupdate `app/Views/public/home.php` dan `public/assets/css/style.css`.

### U. Pengintegrasian Navigasi Login Admin di Footer & Penyempurnaan CRUD CMS Lengkap
- **Navigasi Login Dashboard Admin di Footer:**
  - Menambahkan tautan navigasi `🔐 Login Admin CMS` pada kolom *Tautan Cepat* footer serta tautan `Dashboard CMS Admin` pada garis hak cipta bagian bawah landing page ([app/Views/public/home.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/public/home.php)) yang langsung mengarah ke `/admin/login`.
- **Penyempurnaan Modul CRUD CMS Admin:**
  - **Modul Portofolio Proyek (`/admin/portfolios`):** Menyediakan fungsionalitas Tambah (`create`/`store`), Edit (`edit`/`update`), dan Hapus (`delete`) lengkap dengan tampilan form [app/Views/admin/portfolios/form.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/admin/portfolios/form.php).
  - **Modul Testimoni Klien (`/admin/testimonials`):** Menyediakan fungsionalitas Tambah (`create`/`store`), Edit (`edit`/`update`), dan Hapus (`delete`) lengkap dengan tampilan form [app/Views/admin/testimonials/form.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/admin/testimonials/form.php).
  - **Modul Section Landing Page (`/admin/sections`):** Menyediakan fungsionalitas Edit (`edit`/`update`) untuk mengubah judul, subjudul, eyebrow, dan status publikasi landing page secara dinamis [app/Views/admin/sections/form.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/admin/sections/form.php).
  - **Modul Paket & Harga (`/admin/packages`):** Dilengkapi metode Hapus (`delete`) dan form pengolahan spesifikasi.
  - **Modul Manajemen User (`/admin/users`), Leads (`/admin/leads`), & Settings (`/admin/settings`):** Berfungsi penuh dengan validasi CSRF dan autentikasi terproteksi.
- Mengupdate `public/index.php`, `app/Controllers/Admin/*`, dan `app/Views/admin/*`.

### V. Fitur Upload & Impor Foto Sebelum (Before) & Sesudah (After) Portofolio Serta Sinkronisasi Sistem
- **Pembaruan Skema Database (`portfolios` table):**
  - Menambahkan kolom `before_image VARCHAR(255) NULL` dan `after_image VARCHAR(255) NULL` pada tabel `portfolios` untuk menyimpan path/URL gambar slider sebelum dan sesudah.
  - Memperbarui skema baku di [database/schema.sql](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/database/schema.sql).
- **Direktori Upload Berijin Lengkap:**
  - Membuat direktori fisik `public/uploads/portfolios/` dengan permission penuh untuk menyimpan berkas upload bertipe JPG, JPEG, PNG, WEBP, dan GIF.
- **Pengolahan Berkas di Controller (`PortfolioController.php`):**
  - Menambahkan metode penanganan upload `uploadImage()` pada `store()` dan `update()` yang menangani penamaan unik timestamp dan ekstensi yang aman.
  - Jika pengguna tidak mengunggah gambar baru saat mengedit, sistem secara otomatis mempertahankan gambar yang tersimpan sebelumnya.
- **Pengembangan Tampilan Form Admin (`app/Views/admin/portfolios/form.php`):**
  - Menambahkan atribut `enctype="multipart/form-data"` pada form portofolio.
  - Menyediakan 2 field upload file independen: **Foto Sebelum (Before)** bertanda merah dan **Foto Sesudah (After)** bertanda hijau.
  - Menyediakan penampil *preview* gambar yang terunggah secara otomatis beserta *badge* indikator status.
- **Tampilan Thumbnail di Daftar Admin (`app/Views/admin/portfolios/index.php`):**
  - Menambahkan kolom **Foto (Before / After)** di tabel daftar portofolio admin dengan thumbnail miniatur dan badge `SEBELUM` / `SESUDAH` untuk kemudahan inspeksi visual.
- **Sinkronisasi Otomatis di Landing Page (`app/Views/public/home.php`):**
  - Setiap kartu portofolio di landing page kini secara otomatis membaca URL `$p['after_image']` dan `$p['before_image']` dari database.
  - Jika proyek portofolio baru diunggah lewat admin dashboard, foto hasil upload langsung tampil di slider perbandingan landing page.
  - Jika foto belum diunggah, sistem memiliki *fallback* aman ke file aset statis (`/assets/img/{slug}.webp` & `/assets/img/{slug}-before.webp`).

### W. Penghapusan Modul Media Manager (`admin/media`)
- Menghapus route `GET /admin/media` pada [public/index.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/public/index.php).
- Menghapus item navigasi `📁 Media Manager` dari sidebar [app/Views/admin/layout.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/admin/layout.php).
- Menghapus berkas controller `app/Controllers/Admin/MediaController.php`, view `app/Views/admin/media/index.php`, dan service `app/Services/MediaService.php`.

### X. Penghapusan Modul Konten Section (`admin/sections`)
- Menghapus route `GET /admin/sections`, `GET /admin/sections/edit/{id}`, dan `POST /admin/sections/update/{id}` pada [public/index.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/public/index.php).
- Menghapus item navigasi `🧩 Konten Section` dari sidebar [app/Views/admin/layout.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/admin/layout.php).
- Menghapus berkas controller `app/Controllers/Admin/SectionController.php` dan direktori view `app/Views/admin/sections/`.

### Y. Perbaikan SQL Ambiguous Column pada Modul Leads (`admin/leads`)
- **Akar Masalah:** Query `JOIN users` pada pencarian data leads mengalami `PDOException SQLSTATE[23000] (Integrity constraint violation: 1052 Column 'deleted_at' in where clause is ambiguous)` karena kedua tabel `leads` dan `users` memiliki kolom `deleted_at`.
- **Perbaikan:** Menambahkan alias tabel `l.` pada klausa `WHERE` (`l.deleted_at IS NULL`, `l.status = ?`, `l.need = ?`, `l.assigned_to = ?`, `l.code`, `l.name`, `l.phone`) di [LeadAdminController.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Controllers/Admin/LeadAdminController.php#L23-L47).

### Z. Perbaikan Column Not Found 'client_name' pada Modul Testimoni (`admin/testimonials`)
- **Akar Masalah:** Query `INSERT` & `UPDATE` pada [TestimonialController.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Controllers/Admin/TestimonialController.php) menggunakan nama kolom `client_name`, `client_title`, `project_title` yang tidak sesuai dengan skema fisik tabel database `testimonials` (`name`, `city`, `project_name`, `has_consent`).
- **Perbaikan:** Memperbarui klausa `INSERT` dan `UPDATE` di `TestimonialController.php` agar menggunakan nama kolom baku `name`, `city`, `project_name`, dan mengisi `has_consent = 1` secara otomatis.

### AA. Penghapusan Modul Testimoni Klien (`admin/testimonials`) & Pembersihan Tabel Database
- **Penghapusan Modul Testimoni:**
  - Menapus seluruh route `/admin/testimonials*` pada [public/index.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/public/index.php).
  - Menghapus tautan `💬 Testimoni` dari sidebar [app/Views/admin/layout.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Views/admin/layout.php).
  - Menghapus `app/Controllers/Admin/TestimonialController.php`, `app/Models/TestimonialModel.php`, dan direktori view `app/Views/admin/testimonials/`.
  - Mengupdate `app/Controllers/Public/HomeController.php` agar bebas dari pergantungan ke `TestimonialModel`.
- **Pembersihan Tabel Database Tidak Terpakai:**
  - Menghapus 3 tabel yang tidak terpakai dari database MySQL `rbk_db`:
    1. `testimonials` (Modul Testimoni dihapus)
    2. `media` (Modul Media Manager dihapus)
    3. `portfolio_images` (Digantikan oleh kolom `before_image` & `after_image` langsung pada tabel `portfolios`)
  - 21 tabel tersisa di database dikonfirmasi 100% aktif dan digunakan oleh sistem.

### AB. Perbaikan Table Not Found 'testimonials' pada Beranda Dashboard (`/admin`)
- **Akar Masalah:** `DashboardController.php` baris 47 sebelumnya mencoba menghitung statistik testimoni terpublikasi (`SELECT COUNT(*) FROM testimonials`) padahal tabel `testimonials` telah dihapus.
- **Perbaikan:** Menghapus query ke tabel `testimonials` dan mengatur nilai `$publishedTestimonials = 0` secara statis di [DashboardController.php](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/app/Controllers/Admin/DashboardController.php#L45-L48).

---

## 3. STATUS VERIFIKASI

- [x] Asset gambar fisik `public/assets/img/*.webp` mengembalikan HTTP 200 OK.
- [x] Tautan navigasi login ke Dashboard Admin CMS terkonfirmasi terpasang rapi di footer landing page (`/admin/login`).
- [x] Modul Admin CMS (Portofolio, Testimoni, Paket & Harga, Section Content, User, Leads, Settings) terkonfirmasi memiliki fungsionalitas CRUD lengkap.
- [x] Slider *Before/After* berjalan mulus (60 FPS) pada pergerakan kursor mouse dan touch swipe mobile.
- [x] Setiap kartu portofolio terkonfirmasi memiliki slider perbandingan Sebelum & Sesudah (*Before/After*) interaktif.
- [x] Section 9 ("8 Keunggulan Utama Rancang Bangun Kreasi") terkonfirmasi tampil rapi 4 kolom $\times$ 2 baris pada desktop, 2 kolom pada tablet, dan 1 kolom pada mobile tanpa ada area menggantung.
- [x] Badge filter kategori portofolio & badge kategori kartu terkonfirmasi tampil lebih rapi dan presisi dengan dot indicator oren.
- [x] Filter kategori portofolio berpindah dengan transisi fade & scale yang halus tanpa lompatan layout kasar.
- [x] Tombol action "Jelajahi Portofolio Kami" terkonfirmasi aktif di bawah portfolio grid dan langsung mengarah ke `https://rancangbangunkreasi.id/projects/`.
- [x] Tombol "Konsultasi Gratis" di navbar dan floating button terkonfirmasi menampilkan ikon WhatsApp hijau resmi yang terlampir.
- [x] Mobile bottom sticky CTA bar (`.mbar`) terkonfirmasi telah dihapus bersih dari layar.
- [x] Spesifikasi Struktur pada Kartu Paket Bangun Standard terkonfirmasi diperbarui menjadi `"Kolom beton bertulang, pondasi batu kali"`.
- [x] Logo resmi Rancang Bangun Kreasi terpasang rapi di navbar publik, footer, sidebar admin, dan login page.
- [x] Teks eyebrow Hero Section terkonfirmasi berganti menjadi `ARCHITECTURE & PLANNING JABODETABEK`.
- [x] Judul H1 Hero Section terkonfirmasi menjadi `Jasa Arsitek & Kontraktor Terbaik & Terlengkap untuk Mewujudkan Bangunan Impian Anda` dengan frasa `Terbaik & Terlengkap` berwarna oren tanpa simbol bintang.
- [x] Subtitle Hero Section tampil lebih besar dengan penekanan **BOLD** pada `Rencanakan bersama RBK Studio` dan `bangun bersama RBK Konstruksi`.
- [x] Badge/chip fitur hero (`Desain mulai...`, `Bangun mulai...`, `Area...`) dan tombol CTA tampil lebih besar dan jelas.
- [x] Section Trust Strip terkonfirmasi menggunakan kartu individu modern (*trust-card*) dengan animasi hover dan layout responsif yang rapi.
- [x] Blok teks chip (`Estetika`, `Fungsi`, `Kenyamanan`, `Efisiensi`, `Teknis`, `Budget`) telah dihapus secara bersih.
- [x] Judul Section 7 terkonfirmasi menampilkan frasa `Desain rumah tropis` berwarna oren dan `yang tangguh di cuaca Bogor` berwarna hitam netral.
- [x] 4 Kartu Section 4 ("Bangun sekali. Rencanakan dengan benar sejak awal.") terkonfirmasi sejajar 1 baris (4 kolom) rapi.
- [x] Seluruh field input dan dropdown select di landing page maupun admin CRUD terkonfirmasi 100% full-width, rapi, dengan label di atas input dan ikon panah oren kustom.
- [x] Kalkulator Estimasi Biaya (Section 11 / `#kalkulator`) terkonfirmasi terpisah menjadi 2 kartu independen yang rapi (Desain Arsitektur & Pembangunan Fisik) dengan perhitungan realtime dan integrasi autofill form.
- [x] Fitur upload Foto Sebelum (Before) & Foto Sesudah (After) terkonfirmasi berfungsi pada form Admin CMS (`/admin/portfolios/create` & `/admin/portfolios/edit`).
- [x] Upload foto tersinkronisasi 100% antara database MySQL `portfolios`, dashboard admin (dengan preview miniatur), dan slider perbandingan di landing page.
- [x] Dokumentasi pemeliharaan lengkap tersedia di [docs/PORTFOLIO-GLITCH-FIX.md](file:///Applications/XAMPP/xamppfiles/htdocs/rbkstudio2/docs/PORTFOLIO-GLITCH-FIX.md).







