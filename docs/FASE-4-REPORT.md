# LAPORAN PENYELESAIAN FASE 4 — ALUR LEAD, UTMS, DIRECT WA & NOTIFIKASI

Dokumen ini mencatat penyelesaian pekerjaan **Fase 4** sesuai spesifikasi Master Prompt.

---

## 1. FORM LEAD 2-LANGKAH (AJAX FLOW)

1. **Langkah 1 (AJAX POST `/api/lead/step1`):**
   - Mengumpulkan Nama, Nomor WhatsApp, dan Kebutuhan Layanan.
   - Menguji Honeypot (`website_url_check`) & meloloskan validasi nomor telepon Indonesia.
   - Menyimpan lead secara langsung ke database dengan `is_complete = 0` dan `status = 'baru'`.
   - Meng-generate Kode Lead unik dengan format `RBK-YYMMDD-XXXX`.
   - Melakukan normalisasi nomor WhatsApp otomatis ke format `62...`.
   - Melakukan pengecekan duplikasi 30 hari: jika nomor WA pernah terdaftar dalam 30 hari terakhir, sistem menandai `is_duplicate = 1` dan menautkan `duplicate_of`.
2. **Langkah 2 (AJAX POST `/api/lead/step2`):**
   - Mengumpulkan Lokasi Proyek, Luas Tanah, Luas Bangunan (m²), Budget Pembangunan, Paket Pilihan, Catatan, dan Checkbox Persetujuan PDP.
   - Menangkap seluruh Atribusi UTM (`utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `fbclid`, `ttclid`, `referrer`, `landing_url`, `device`, `first_touch` JSON, `calc_snapshot` JSON).
   - Mengubah status lead menjadi `is_complete = 1`.
   - Mengarahkan pengunjung ke `/terima-kasih?kode=RBK-YYMMDD-XXXX`.
   - Menarik tombol "Lewati & Chat Sekarang" untuk langsung menuju Halaman Terima Kasih.

---

## 2. HALAMAN TERIMA KASIH & DIRECT WA CLICK

- **Halaman Terima Kasih (`/terima-kasih`):**
  - Menampilkan ringkasan data submission lead dan Kode Referensi.
  - Memicu event konversi otomatis (`generate_lead`, Google Ads conversion, Meta Lead Event).
  - Tombol "Lanjut Chat di WhatsApp" dan skrip redirect otomatis setelah 3 detik.
  - Format pesan WhatsApp dibentuk dinamis berdasarkan template di Pengaturan.
- **Direct WA Click Analytics (`/api/lead/wa-click`):**
  - Mencatat setiap interaksi tombol WhatsApp melayang dan bar mobile ke tabel `wa_clicks` secara anonim tanpa data pribadi.

---

## 3. NOTIFIKASI LEAD BARU (`NotificationService`)

- **Email Notification:** Pengiriman email notifikasi HTML via PHPMailer SMTP ke daftar penerima admin.
- **Telegram Bot Notification:** Pengiriman notifikasi instant pesan Telegram Markdown jika token bot & Chat ID terkonfigurasi.

---

*Fase 4 Selesai dan Siap Dilanjutkan ke Fase 5 (Semua Modul CRUD Admin Dashboard).*
