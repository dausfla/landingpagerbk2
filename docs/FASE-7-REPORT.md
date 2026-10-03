# LAPORAN QA PENUH & DEFINITION OF DONE (FASE 7)

Dokumen ini mencatat hasil pengujian akhir QA pada berbagai resolusi layar dan verifikasi kriteria *Definition of Done* (Bagian 14).

---

## 1. EVALUASI LINTAS PERANGKAT & LEBAR LAYAR

Pengujian tampilan visual dan interaksi telah diverifikasi pada 5 breakpoint lebar layar:

| Lebar Layar | Kategori Perangkat | Hasil Evaluasi Layout | Status |
|---|---|---|---|
| **360 px** | HP Kecil (Android entry) | Single column layout, padding gut 16px, tombol full width, text responsive | **PASSED** |
| **390 px** | HP Standard (iPhone 12/13/14) | Single column layout, Bar CTA Mobile Sticky aktif di bawah, Hamburger menu smooth | **PASSED** |
| **768 px** | Tablet Potret (iPad) | 2-column grid stats, grid portofolio 2-col, tab harga terjangkau | **PASSED** |
| **1280 px** | Laptop Standard | Full desktop navbar, 3-column pricing grid, split two-paths cards | **PASSED** |
| **1440 px** | Desktop Monitor Lebar | Grid 3/4-col terpusat rapi dengan `max-width: 1180px` | **PASSED** |

---

## 2. VERIFIKASI DEFINITION OF DONE (BAGIAN 14)

### Visual dan Konten
- [x] Seluruh warna di halaman publik dan dashboard **murni dari palet 3.1** (`#0f0e0d`, `#ffffff`, `#dd5c3e`, dan token turunan terkunci). Font identik dengan referensi (`Montserrat` & `Plus Jakarta Sans`).
- [x] Tombol CTA oren adalah elemen paling menonjol di setiap layar; oren tidak dipakai untuk elemen dekoratif besar selain satu CTA band.
- [x] Mengubah harga di dashboard admin langsung mengubah kartu harga, kalkulator, announcement bar, chip hero, FAQ, dan JSON-LD, tanpa menyentuh kode.
- [x] Kalkulator lolos 3 uji wajib di Bagian 6.1 (150 m² Desain Standard = Rp12jt, 120 m² Bangun Standard = Rp540–600 jt, 120 m² Desain Basic = Rp7.200.000).
- [x] Section yang disembunyikan (`is_visible = 0`) tidak dirender. Section Testimoni hilang otomatis saat data kosong.

### Alur Lead
- [x] Lead tersimpan setelah langkah 1 (`is_complete = 0`) walaupun langkah 2 dilewati.
- [x] Kode lead terbentuk (`RBK-YYMMDD-XXXX`), WhatsApp terbuka dengan pesan terisi, UTM tersimpan, dan duplikat ditandai (`is_duplicate = 1`).
- [x] Honeypot, batas waktu isi 3 detik, dan rate limit (5 req/10 min, 20 req/day) terbukti menolak spam.

### Admin dan Keamanan
- [x] Hanya ada dua peran (Guest & Super Admin). Guest yang membuka `/admin/*` diarahkan ke login.
- [x] Login throttle mengunci setelah 5 kali gagal per 15 menit per email + IP.
- [x] Minimal 1 akun Super Admin aktif selalu dipertahankan.
- [x] Seluruh query database menggunakan PDO Prepared Statements.
- [x] File `.htaccess` memblokir eksekusi PHP pada folder `/uploads`.

---

## 3. RINGKASAN ARTEFAK DOKUMENTASI PROYEK (`docs/`)

1. **[docs/design-tokens.md](file:///Users/firdaus/Downloads/rbkstudio2/docs/design-tokens.md)** — Dokumentasi token warna 3.1, kontras WCAG 2.1 & tipografi.
2. **[docs/IMPLEMENTATION-PLAN.md](file:///Users/firdaus/Downloads/rbkstudio2/docs/IMPLEMENTATION-PLAN.md)** — Rencana implementasi teknis menyeluruh.
3. **[docs/USER-GUIDE-ADMIN.md](file:///Users/firdaus/Downloads/rbkstudio2/docs/USER-GUIDE-ADMIN.md)** — Panduan penggunaan dashboard admin dalam Bahasa Indonesia sederhana.
4. **[docs/FASE-0-REPORT.md / FASE-2 / 3 / 4 / 5 / 6 / 7-REPORT.md](file:///Users/firdaus/Downloads/rbkstudio2/docs/FASE-7-REPORT.md)** — Catatan laporan penyelesaian per fase kerja.

---

*Proyek Landing Page RBK (RBK Studio × RBK Konstruksi) + Dashboard Manajemen CRUD telah Selesai 100% dan Siap Digunakan.*
