# IMPLEMENTATION PLAN — RBK LANDING PAGE & ADMIN CRUD DASHBOARD

Dokumen ini memuat rencana implementasi teknis secara menyeluruh untuk membangun Landing Page Rancang Bangun Kreasi (RBK Studio × RBK Konstruksi) beserta Dashboard Admin CRUD berbasis **PHP 8.2+ Native** dan **MySQL 8.0**.

---

## 1. STRUKTUR ARSITEKTUR SOFTWARE & STRUKTUR FOLDER

Aplikasi dibangun menggunakan pola arsitektur **MVC Ringan (Model-View-Controller)** tanpa framework berat, menjamin kecepatan eksekusi tinggi dan kemudahan pemeliharaan di server shared hosting cPanel maupun VPS Nginx.

```
/app
  /Core
    - Router.php          (Routing URL berbasis REST & wildcard)
    - Request.php         (Wrapper HTTP Request, sanitasi & IP hash)
    - Response.php        (JSON helper, Redirect, HTTP headers)
    - DB.php              (Singleton PDO PDO::ERRMODE_EXCEPTION, Emulate Prepare = False)
    - Auth.php            (Manajemen sesi Super Admin, Argon2id/Bcrypt hash)
    - Csrf.php            (Token Anti-CSRF per session)
    - Validator.php       (Validasi data form sisi server)
    - View.php            (Renderer PHP Template + auto-escaping helper e())
    - Cache.php           (HTML Static Page Caching & Cache Invalidation)
    - Mailer.php          (Wrapper PHPMailer SMTP untuk notifikasi lead)
  /Controllers
    /Public
      - HomeController.php       (Render landing page S0-S21 & dinamis JSON pricing)
      - LeadController.php       (AJAX handling Form Step 1 & Step 2, kode lead generation)
      - CalculatorController.php (Biaya estimasi API calculation snapshot)
      - TrackingController.php   (Logging WA click, phone click, event tracking)
      - PageController.php       (Halaman /terima-kasih, /kebijakan-privasi, sitemap, robots)
    /Admin
      - AuthController.php       (Login, Logout, Reset Password)
      - DashboardController.php  (KPI Stats, Lead Response Median, Go-Live Checklist)
      - LeadAdminController.php  (CRUD Lead, Detail, Pipeline Change, CSV Export)
      - ContentController.php    (Section, Layanan, Keunggulan, Alur, FAQ, Artikel, Stat)
      - PackageController.php    (CRUD Paket Desain & Bangun + Specs & Price History)
      - PortfolioController.php  (CRUD Portofolio & Upload Sebelum/Sesudah)
      - TestimonialController.php(CRUD Testimoni + Consent Checkbox)
      - MediaController.php      (Upload, WebP 3-variant conversion, usage check)
      - SettingsController.php   (Tab Umum, Kontak, SEO, Tracking, SMTP, WA Template)
      - UserController.php       (CRUD Super Admin Accounts)
      - AuditLogController.php   (Activity Logs)
  /Models
    - UserModel.php, SettingModel.php, SectionModel.php, MediaModel.php
    - ServiceModel.php, PackageModel.php, PortfolioModel.php, AdvantageModel.php
    - ProcessModel.php, TestimonialModel.php, StatModel.php, FaqModel.php
    - ArticleModel.php, LeadModel.php, WaClickModel.php, ActivityLogModel.php
  /Middleware
    - AuthMiddleware.php  (Memastikan hanya Super Admin login yang dapat akses /admin)
    - CsrfMiddleware.php  (Verifikasi token CSRF pada POST/PUT/DELETE)
    - RateLimitMiddleware.php (Max 5 req/10 min & 20 req/day per IP hash)
  /Services
    - LeadService.php     (Logika bisnis lead, deduplikasi 30 hari, atribusi UTM)
    - PricingService.php  (Kalkulasi paket & replace placeholder FAQ/Announcement)
    - MediaService.php    (Resizing GD/Imagick ke WebP 480px, 960px, 1600px)
    - NotificationService.php (Kirim email SMTP via PHPMailer & Telegram bot)
  /Views
    /public
      - home.php, terima-kasih.php, kebijakan-privasi.php, 404.php
    /admin
      - login.php, dashboard.php, styleguide.php
      - /leads (index, detail)
      - /packages (index, form)
      - /portfolios (index, form)
      - /sections, /services, /advantages, /process, /testimonials, /stats, /faqs, /articles
      - /media, /settings, /users, /activity_logs
    /partials
      - header.php, footer.php, nav.php, mbar.php, whatsapp_float.php, calc.php
/config
  - app.php, database.php
/database
  - schema.sql, seed.sql
/public
  - index.php, .htaccess, robots.txt, sitemap.xml
  /assets (/css/tokens.css, /css/style.css, /js/main.js, /js/calc.js, /fonts)
  /uploads
/storage
  /cache, /logs, /seed-images
/scripts
  - install.php, backup.php
/docs
  - design-tokens.md, IMPLEMENTATION-PLAN.md, USER-GUIDE-ADMIN.md
```

---

## 2. SKEMA DATABASE & HUBUNGAN ENTITAS (MYSQL 8.0)

Semua tabel menggunakan **InnoDB**, `utf8mb4_unicode_ci`, dan mendukung **soft delete** (`deleted_at`). Harga disimpan dalam format `BIGINT UNSIGNED` (Rupiah utuh).

```
                      +-------------------+
                      |       users       |
                      +-------------------+
                                | 1
                                | assigned_to / uploaded_by / changed_by
                                v
+------------------+  +-------------------+  +------------------------+
|     settings     |  |       leads       |--|    lead_activities     |
+------------------+  +-------------------+  +------------------------+
                                | 1
                                v
+------------------+  +-------------------+  +------------------------+
|     packages     |--|   package_specs   |  | package_price_history  |
+------------------+  +-------------------+  +------------------------+
                                
+------------------+  +-------------------+  +------------------------+
|    portfolios    |--| portfolio_images  |--|         media          |
+------------------+  +-------------------+  +------------------------+
```

---

## 3. RENDERING LANDING PAGE (S0 - S21) & MODUL DYNAMIC SECTIONS

Setiap section pada landing page dipetakan ke record di tabel `sections` (`key` unik). Jika `is_visible = 0`, section **tidak dirender sama sekali** di HTML server-side dan otomatis tidak muncul di menu navigasi.

| Key | Section Anchor | Fitur & Sumber Data |
|---|---|---|
| `s0_announcement` | Announcement Bar | Text dengan placeholder harga terisi dinamis dari `packages`. |
| `s1_navbar` | Header Nav | Sticky desktop & mobile hamburger menu. |
| `s2_hero` | Hero `#top` | Headline, chip area/harga dinamis, gambar La Bella (LCP preloaded). |
| `s3_trust` | Trust Strip | Counter animasi 4 stats dari `stats`. |
| `s4_problem` | Masalah `#kenapa` | 4 pain points & CTA. |
| `s5_two_paths` | Dua Jalur `#layanan` | Kartu RBK Studio (PLAN) vs RBK Konstruksi (BUILD) + Design&Build. |
| `s6_studio_scope` | Lingkup Studio | 6 lingkup layanan + chip filosofi. |
| `s7_tropical_bogor` | Desain Tropis Bogor | 5 poin desain tropis Bogor + 2 foto proyek. |
| `s8_portfolio` | Portofolio `#portofolio` | Before/After slider + category filter + project grid. |
| `s9_advantages` | Keunggulan `#keunggulan` | 8 keunggulan kurasi A + B. |
| `s10_pricing` | Harga `#harga` | Tab Desain vs Bangun + Tabel Spesifikasi Material. |
| `s11_calculator` | Kalkulator `#kalkulator` | 3 mode (Desain/Bangun/Both) + Rentang Biaya Realtime JS. |
| `s12_process` | Alur Kerja `#proses` | 8 langkah alur kerja + Toggle Desain Saja (1-6). |
| `s13_survey` | Survei Gratis | 3 poin survei lokasi gratis. |
| `s14_testimonials` | Testimoni | Otomatis tersembunyi jika data `testimonials` publikasi = 0. |
| `s15_cta_band` | CTA Band | Background oren murni "PLAN FIRST. BUILD ONCE." |
| `s16_lead_form` | Form `#konsultasi` | Form 2 Langkah + Kontak + Maps Lazy-loaded. |
| `s17_about` | Tentang `#tentang` | Profil PT & 4 nilai perusahaan. |
| `s18_articles` | Artikel | 3 kartu artikel blog `articles`. |
| `s19_faq` | FAQ `#faq` | Accordion `<details>` dengan placeholder harga ter-replace. |
| `s20_closer` | Penutup | Call to action penutup. |
| `s21_footer` | Footer | Logo, copyright dinamis, legal links. |

---

## 4. FORM LEAD 2-LANGKAH, ATRIBUSI UTM & ALUR WHATSAPP

1. **Langkah 1 (AJAX POST /api/lead/step1):**
   - Field: Nama, WhatsApp (normalisasi format 62...), Kebutuhan.
   - Tersimpan langsung ke DB dengan `is_complete = 0`.
   - Mengembalikan `lead_id` & `code` (format `RBK-YYMMDD-XXXX`).
2. **Langkah 2 (AJAX POST /api/lead/step2):**
   - Field: Lokasi, Luas Tanah, Luas Bangunan, Jumlah Lantai, Style, Budget, Paket Pilihan, Catatan, Consent Checkbox.
   - Update lead menjadi `is_complete = 1`.
   - Simpan data atribusi UTM (`utm_source`, `utm_medium`, `gclid`, `fbclid`, `device`, `first_touch` JSON, `calc_snapshot` JSON).
   - Redirect ke `/terima-kasih?kode=RBK-YYMMDD-XXXX`.
3. **Pemberitahuan & Direct WA:**
   - Email SMTP dikirim via PHPMailer & Notifikasi Telegram Bot terpotong secara otomatis.
   - Tombol "Lanjut Chat di WhatsApp" di Halaman Terima Kasih memicu tautan `wa.me/6281234593742?text=...` berdasar template di Pengaturan.
4. **Keamanan Form:**
   - Field Honeypot (`website_url_check`).
   - Minimum submit time threshold (3 detik).
   - Token Anti-CSRF.
   - Deduplikasi nomor WhatsApp dalam 30 hari (`is_duplicate = 1`).

---

## 5. FITUR KALKULATOR BIAYA ESTIMASI

- **Dynamic Data Injection:** Server meng-inject JSON dari database ke `<script type="application/json" id="pricing-data">`.
- **Mode:**
  - Mode 1: Desain Saja (`Luas x Price_Min`)
  - Mode 2: Bangun Saja (`Luas x Price_Min` s/d `Luas x Price_Max`)
  - Mode 3: Desain + Bangun (Penjumlahan keduanya)
- **Formatting Helper:**
  - Rp12.000.000 (Angka pasti)
  - Rp540–600 jt (Rentang Juta)
  - Rp1,2–1,35 M (Rentang Miliar)
- **CTA Action:** Menekan "Kirim estimasi ini & minta RAB gratis" otomatis memilih dropdown form, mengisi hidden `calc_snapshot`, dan melakukan smooth scroll ke form `#konsultasi`.

---

## 6. DASHBOARD ADMIN CRUD (`/admin`)

- **Hak Akses:** Hanya 2 Peran (Guest & Super Admin). `AuthMiddleware` melindungi semua URL `/admin/*`.
- **Go-Live Checklist Widget:** Menampilkan warning item `[VERIFIKASI]`, tracking ID kosong, testimoni 0, password default, status SMTP test.
- **Median Response Time:** Dihitung dari selisih `created_at` ke `first_contacted_at` pada jam kerja (Mon-Sat 08:00-17:00).
- **Aturan Warna Admin (Aturan 8.2):** Tanpa warna status hijau/kuning/merah tambahan. Status baru memakai Oren penuh, Dihubungi s/d Penawaran memakai Outline Hitam, Deal memakai Hitam penuh + `✓`, Batal memakai Abu bercoret.
- **Cache Invalidation:** Setiap penambahan/perubahan data di dashboard admin secara otomatis menghapus cache file HTML landing page publik.

---

## 7. CHECKLIST INTEGRASI & ROADMAP EKSEKUSI FASE 1

- [x] Dokumen Design Tokens (`docs/design-tokens.md`)
- [x] Style CSS Variables (`public/assets/css/tokens.css`)
- [x] Preview Visual Verification (`public/styleguide.php`)
- [ ] Rencana Implementasi Detail (`docs/IMPLEMENTATION-PLAN.md`)
- [ ] Skema Database MySQL (`database/schema.sql`)
- [ ] Data Seed Konten Referensi A & B (`database/seed.sql`)

---

*Dokumen Rencana Implementasi ini disusun pada Fase 1 sebagai panduan resmi pembangunan sistem RBK.*
