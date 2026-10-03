# DOKUMEN DESIGN TOKENS — RBK STUDIO × RBK KONSTRUKSI

Dokumen ini adalah acuan resmi (*single source of truth*) untuk seluruh token desain, sistem warna, tipografi, spacing, komponen, dan peraturannya pada Landing Page RBK (RBK Studio × RBK Konstruksi) dan Dashboard Admin CRUD.

---

## 1. PALET WARNA TERKUNCI (DIKUNCI OLEH OWNER — SECTION 3.1)

Palet hanya terdiri dari **tiga warna inti** dan **delapan token turunan** yang dibuat murni dari pencampuran ketiga warna inti tersebut. Dilarang keras menambah hue atau warna lain.

### 1.1 Warna Inti

| Token CSS | Hex | RBR Formula | Peran & Penggunaan |
|---|---|---|---|
| `--color-black` | `#0f0e0d` | Hitam murni (murni ink) | Latar belakang gelap (hero, CTA band, footer, section kontras), teks utama di latar terang, header tabel. |
| `--color-white` | `#ffffff` | Putih murni | Latar utama terang, teks di atas latar gelap, latar kartu. |
| `--color-orange` | `#dd5c3e` | Oren murni | Aksen utama: CTA utama, harga & angka statistik, badge, ikon, garis aksen, status aktif (tab, chip, menu), kata miring `<em>` pada heading di latar gelap. |

### 1.2 Token Turunan

| Token CSS | Hex | Rumus Campuran | Dipakai Untuk |
|---|---|---|---|
| `--color-orange-strong` | `#b44c34` | Oren 80% + Hitam 20% | Hover tombol oren; teks oren berukuran kecil (< 24px regular / < 18.66px bold) di latar terang untuk memenuhi WCAG 2.1. |
| `--color-orange-soft` | `#fcf2f0` | Oren 8% + Putih 92% | Latar kartu paket "Recommended", latar highlight, fokus field form. |
| `--color-muted-light` | `#656564` | Hitam 64% + Putih | Teks sekunder/subjudul di latar terang, placeholder, timestamp, teks batal. |
| `--color-muted-dark` | `#b2b2b2` | Putih 68% + Hitam | Teks sekunder/subjudul di latar gelap. |
| `--color-border-light` | `#e2e2e2` | Hitam 12% + Putih | Garis pemisah dan border di latar terang. |
| `--color-border-dark` | `#31302f` | Putih 14% + Hitam | Garis pemisah dan border di latar gelap. |
| `--color-surface-light` | `#f5f5f5` | Hitam 4% + Putih | Section terang selang-seling, latar input, latar tabel alternate. |
| `--color-surface-dark` | `#1d1c1c` | Putih 6% + Hitam | Kartu di atas latar gelap, modal admin, dropdown menu. |

---

## 2. PEMETAAN WARNA REFERENSI KE PALET TERKUNCI 3.1

Dalam audit Referensi A (`https://rbk-jasaarsitek.netlify.app/`) dan Referensi B (`https://dynamic-kataifi-5188a8.netlify.app/`), ditemukan beberapa warna asal yang digunakan. Seluruh warna asal dipetakan langsung ke palet terkunci di bawah ini tanpa menambahkan hue baru:

| Warna di Referensi A/B | Hex Asal | Sumber | Dipetakan Ke Token 3.1 | Hex Terkunci | Alasan Pemetaan |
|---|---|---|---|---|---|
| `--ink` | `#0f0e0d` | A & B | `--color-black` | `#0f0e0d` | Identik |
| `--ink-2` | `#1b1a18` | A & B | `--color-black` / `--color-surface-dark` | `#0f0e0d` / `#1d1c1c` | Diseragamkan ke permukaan gelap terkunci |
| `--ink-3` | `#2a2826` | A & B | `--color-border-dark` | `#31302f` | Garis pada permukaan gelap |
| `--paper` | `#f3f1ec` | A & B | `--color-surface-light` | `#f5f5f5` | Krem hangat dipetakan ke surface light bersih |
| `--paper-2` | `#e9e6df` | A & B | `--color-surface-light` / `--color-border-light` | `#f5f5f5` / `#e2e2e2` | Latar sekunder terang |
| `--card` | `#ffffff` | A & B | `--color-white` | `#ffffff` | Identik |
| `--line` | `#d8d3ca` | A & B | `--color-border-light` | `#e2e2e2` | Border di latar terang |
| `--line-dk` | `#34312d` | A & B | `--color-border-dark` | `#31302f` | Border di latar gelap |
| `--fg` | `#171614` | A & B | `--color-black` | `#0f0e0d` | Teks utama |
| `--muted` | `#57524b` | A & B | `--color-muted-light` | `#656564` | Teks sekunder terang |
| `--muted-dk` | `#a8a197` | A & B | `--color-muted-dark` | `#b2b2b2` | Teks sekunder gelap |
| `--orange` | `#dd5c3e` | A & B | `--color-orange` | `#dd5c3e` | Identik |
| `--orange-dk` | `#b8442a` | A & B | `--color-orange-strong` | `#b44c34` | Dipetakan ke orange-strong terkunci |
| `--orange-soft` | `#fbe7e0` | A & B | `--color-orange-soft` | `#fcf2f0` | Dipetakan ke orange-soft terkunci |
| `--ok` | `#2f7d4f` | A & B | `--color-black` + ikon `✓` | `#0f0e0d` | Status deal/sukses memakai hitam + centang (aturan 8.2 tanpa warna hijau/kuning/merah) |

---

## 3. ATRUAN KONTRAS (WCAG 2.1) DAN PROPORSI DESAIN

### 3.1 Tabel Evaluasi Kontras (WCAG 2.1 Compliance)

| Kombinasi Elemen | Rasio Kontras | Level WCAG | Ketentuan Penggunaan |
|---|---|---|---|
| Hitam (`#0f0e0d`) di atas Putih (`#ffffff`) | 19.28 : 1 | AAA (Bebas) | Teks body utama, heading latar terang, label field. |
| Oren (`#dd5c3e`) di atas Hitam (`#0f0e0d`) | 5.21 : 1 | AA (Bebas) | Teks miring `<em>` di heading gelap, harga di card gelap, badge di background gelap. |
| Oren (`#dd5c3e`) di atas Putih (`#ffffff`) | **3.70 : 1** | AA Large Only | **HANYA** untuk teks besar (≥ 24px regular atau ≥ 18.66px bold), ikon, garis aksen, border. |
| Oren Strong (`#b44c34`) di atas Putih (`#ffffff`) | **5.22 : 1** | AA Normal Text | **WAJIB** untuk teks oren berukuran kecil (< 24px) di latar terang (misal badge kecil, tagline, alert error). |
| Label Hitam (`#0f0e0d`) di atas Tombol Oren (`#dd5c3e`) | 5.21 : 1 | AA (Bebas) | **Default** label untuk tombol CTA oren utama. |
| Label Putih (`#ffffff`) di atas Tombol Oren (`#dd5c3e`) | 3.70 : 1 | AA Large Only | Hanya jika label berukuran ≥ 18.66px bold. |

### 3.2 Aturan Proporsi Visual 60 - 30 - 10

1. **60% Putih (`#ffffff` / `--color-surface-light`):** Dominasi latar belakang utama halaman agar terkesan bersih, lapang, dan mudah dibaca.
2. **30% Hitam (`#0f0e0d` / `--color-surface-dark`):** Latar hero, CTA band, footer, header tabel, serta warna teks utama untuk menciptakan kontras tinggi dan kesan premium yang tenang.
3. **10% Oren (`#dd5c3e`):** Aksen yang **sangat langka dan terukur** agar tombol CTA utama memiliki daya tarik visual tertinggi (*maximum visual hierarchy*).
4. Blok latar oren penuh maksimal **satu** di seluruh halaman (CTA band "PLAN FIRST. BUILD ONCE.").
5. Tombol sekunder menggunakan outline hitam (di latar terang) atau outline putih (di latar gelap).
6. Tombol Floating WhatsApp memakai warna oren dengan ikon WhatsApp putih/hitam.

---

## 4. SISTEM TIPOGRAFI

Font di-self-host menggunakan format `.woff2` dengan `font-display: swap`.

### 4.1 Font Families

| Peran | Font Family | Fallback Stack | Sumber Referensi |
|---|---|---|---|
| **Heading / Display** | `Montserrat` | `"Segoe UI", system-ui, -apple-system, sans-serif` | Referensi A & B |
| **Body / Text** | `Plus Jakarta Sans` | `system-ui, -apple-system, "Segoe UI", sans-serif` | Referensi A & B |
| **Mono / Numbers** | `Plus Jakarta Sans` | `system-ui, -apple-system, monospace` | Referensi A & B |

### 4.2 Skala Ukuran Font & Hierarchy

| Token CSS | Ukuran (px / rem) | Font Weight | Line Height | Letter Spacing | Dipakai Untuk |
|---|---|---|---|---|---|
| `--fs-h1` | `clamp(32px, 5vw, 54px)` | 800 (ExtraBold) | 1.05 | `-0.02em` | H1 Hero utama |
| `--fs-h2` | `clamp(24px, 3.8vw, 38px)` | 800 (ExtraBold) | 1.15 | `-0.015em` | H2 Section title |
| `--fs-h3` | `clamp(18px, 2.5vw, 24px)` | 700 (Bold) | 1.25 | `-0.01em` | H3 Card title, Paket, Dua Jalur |
| `--fs-h4` | `18px` | 700 (Bold) | 1.3 | `0em` | Sub-card title, FAQ summary |
| `--fs-body-lg` | `17px` | 500 (Medium) | 1.6 | `0em` | Subjudul Hero, Lead paragraph |
| `--fs-body` | `15px` | 500 / 600 | 1.55 | `0em` | Teks body standar, list deskripsi |
| `--fs-body-sm` | `13.5px` | 500 / 600 | 1.5 | `0em` | Teks sekunder, caption, footer note |
| `--fs-eyebrow` | `12px` | 700 (Bold) | 1.2 | `0.1em` (UPPERCASE) | Eyebrow label di atas H2 section |
| `--fs-badge` | `11.5px` | 700 (Bold) | 1.0 | `0.05em` | Chip filter, badge status, tag paket |

---

## 5. SYSTEM CONTAINER, SPACING & BREAKPOINTS

### 5.1 Container & Gutters

- `--container-max`: `1180px`
- `--container-narrow`: `860px` (untuk Form, FAQ, Artikel detail)
- `--gut`: `clamp(16px, 4vw, 44px)`

### 5.2 Skala Spacing (`--space-*`)

| Token CSS | Nilai (px) | Penggunaan |
|---|---|---|
| `--space-1` | `4px` | Spacing antar badge/icon |
| `--space-2` | `8px` | Inset kecil, gap chip |
| `--space-3` | `12px` | Inset tombol kecil, gap card list |
| `--space-4` | `16px` | Padding input, gap grid rapat |
| `--space-5` | `20px` | Padding card standar |
| `--space-6` | `24px` | Padding card besar |
| `--space-8` | `32px` | Spacing antar elemen section |
| `--space-10` | `40px` | Margin top/bottom section medium |
| `--space-12` | `48px` | Vertical padding section standar |
| `--space-16` | `64px` | Vertical padding section besar (Hero, CTA Band) |

### 5.3 Breakpoint Responsif

| Breakpoint | Target Layar | Layout Behavior |
|---|---|---|
| `max-width: 520px` | HP Kecil (360-390px) | Single column full width, grid form 1-col, padding gut 16px. |
| `max-width: 760px` | HP Besar / Tablet Potret | Bar CTA Mobile Sticky aktif, Hero stack 1-col, Nav hamburger. |
| `max-width: 900px` | Tablet Lanskap | Grid paket 1-col / slider active, split section stacked. |
| `max-width: 980px` | Laptop Kecil | Desktop nav compact, stat grid 2-col. |
| `min-width: 1181px` | Desktop Standard | Full grid 3-col/4-col dengan max-width 1180px. |

---

## 6. SKALA RADII, SHADOW, TRANSIEN & EASING

### 6.1 Border Radius (`--radius-*`)

- `--radius-xs`: `4px` (Badge kecil, checkbox)
- `--radius-sm`: `6px` (Tag paket, input field)
- `--radius-md`: `10px` (Tombol compact, thumbnail gambar)
- `--radius-lg`: `12px` (Card standar, dropdown menu)
- `--radius-xl`: `14px` (`--r` dari referensi: Card utama, modal popup)
- `--radius-2xl`: `18px` (Container hero visual, float bar)
- `--radius-full`: `999px` (Tombol pill standar, chip filter)

### 6.2 Box Shadow (`--shadow-*`)

- `--shadow-sm`: `0 2px 8px rgba(15, 14, 13, 0.04)`
- `--shadow-md`: `0 4px 16px rgba(15, 14, 13, 0.08)`
- `--shadow-lg`: `0 12px 32px rgba(15, 14, 13, 0.12)`
- `--shadow-orange`: `0 6px 20px rgba(221, 92, 62, 0.25)`

### 6.3 Animasi & Transisi (`--ease-*` & `--duration-*`)

- `--ease-smooth`: `cubic-bezier(0.16, 1, 0.3, 1)`
- `--duration-fast`: `0.15s` (Hover state, focus state)
- `--duration-normal`: `0.25s` (Modal open, tab switch, accordion expand)
- `--duration-slow`: `0.4s` (Scroll reveal, header shrink)

---

## 7. SPESIFIKASI GAYA KOMPONEN DOKUMEN

### 7.1 Tombol (Button Styles)

1. **Primary Button (`.btn-primary` / `.btn.or`):**
   - Background: `--color-orange` (`#dd5c3e`)
   - Text Color: `--color-black` (`#0f0e0d`) (Rasio 5.21:1)
   - Hover: Background `--color-orange-strong` (`#b44c34`), transform `translateY(-2px)`
   - Border Radius: `--radius-full` (`999px`)
   - Padding: `14px 22px`
2. **Secondary Outline Button (`.btn-outline`):**
   - Latar terang: Border `2px solid --color-black`, Teks `--color-black`
   - Latar gelap: Border `2px solid --color-white`, Teks `--color-white`
   - Hover: Latar terbalik (Hitam di terang, Putih di gelap)
3. **WhatsApp Floating & Mobile Button:**
   - Background: `--color-orange` (`#dd5c3e`)
   - Text/Icon: Putih / Hitam
   - Shadow: `--shadow-orange`

### 7.2 Kartu (Card Styles)

- Background: `--color-white` (di latar terang) / `--color-surface-dark` (di latar gelap)
- Border: `1px solid --color-border-light` (terang) / `1px solid --color-border-dark` (gelap)
- Border Radius: `--radius-xl` (`14px`)
- Padding: `--space-6` (`24px`)
- Hover: Shadow elevation ke `--shadow-md` & border color transition.

---

*Dokumen ini dibuat dan divalidasi pada Fase 0 untuk menjamin kepatuhan 100% terhadap instruksi Master Prompt RBK.*
