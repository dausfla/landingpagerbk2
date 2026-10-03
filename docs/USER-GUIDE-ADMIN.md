# PANDUAN PENGGUNAAN DASHBOARD ADMIN (USER GUIDE)
**Rancang Bangun Kreasi (RBK Studio × RBK Konstruksi)**

Dokumen ini adalah panduan praktis bagi tim pengelola/admin untuk mengoperasikan Dashboard Admin Website RBK.

---

## 1. CARA LOGIN & KEAMANAN AKUN

1. Buka browser dan akses alamat: `https://domain-anda.com/admin/login`
2. Masukkan **Email** dan **Password** Super Admin Anda.
3. Setelah berhasil masuk, Anda akan diarahkan ke **Beranda Dashboard**.
4. **Keamanan:** Sesi login akan berakhir otomatis setelah 2 jam tidak ada aktivitas (idle).

---

## 2. MEMAHAMI BERANDA DASHBOARD & GO-LIVE CHECKLIST

Pada **Beranda Dashboard**, Anda dapat melihat ringkasan performa website:
- **KPI Stats:** Jumlah Lead Hari Ini, 7 Hari, 30 Hari, % Kelengkapan Form Step 2, Klik WhatsApp, dan Median Waktu Respons Lead.
- **Widget Checklist Go-Live:** Petunjuk status kesiapan website:
  - *Data Klaim Belum Terverifikasi:* Menunjukkan adanya data statistik bertanda `is_verified = 0`.
  - *Konfigurasi Tracking ID:* Mengingatkan jika ID GTM/GA4/Meta Pixel belum terisi.
  - *Status Testimoni:* Jika belum ada testimoni yang dipublikasikan, section testimoni otomatis disembunyikan.

---

## 3. MENGELOLA DATA LEADS (PERMINTAAN KONSULTASI)

1. Masuk ke menu **Data Leads** pada navigasi kiri.
2. Anda dapat memfilter lead berdasarkan **Status Pipeline**, **Kebutuhan Layanan**, **Pencarian Nama/WA/Kode**, atau **PIC**.
3. Klik tombol **Detail** pada baris lead untuk melihat:
   - Informasi lengkap pemohon (Nama, WA, Lokasi, Rencana Luas, Budget).
   - Data Atribusi Tracking (UTM Source, Medium, Campaign, Perangkat).
   - Snapshot simulasi Kalkulator Biaya yang dihitung klien.
4. **Mengubah Status Pipeline:**
   - Pilih status baru (misal: *Dihubungi*, *Survei Dijadwalkan*, *Deal*, *Batal*).
   - Jika status diubah ke **Batal**, pilih **Alasan Batal** (Harga, Waktu, Lokasi di luar area, dll).
   - Isi tanggal follow-up berikutnya dan sertakan catatan hasil komunikasi.
   - Klik **Simpan Perubahan Pipeline**.
5. **Export Data:** Klik tombol **Export CSV** di kanan atas untuk mengunduh seluruh data lead ke format Excel/CSV.

---

## 4. MENGUBAH HARGA PAKET (PAKET & HARGA)

1. Masuk ke menu **Paket & Harga**.
2. Pilih paket yang ingin diubah (misal: *Paket Standard Desain* atau *Paket Standard Bangun*) lalu klik **Edit**.
3. Ubah nilai **Harga Minimum** atau **Harga Maksimal** (per m²).
4. Klik **Simpan Paket & Update Cache**.
5. *Sistem akan secara otomatis memperbarui angka harga di seluruh halaman publik (kartu harga, kalkulator, announcement bar, FAQ, dan schema Google) tanpa perlu mengubah kode.*

---

## 5. MENGATUR PENGATURAN SISTEM & TRACKING ID

1. Masuk ke menu **Pengaturan Sistem**.
2. Gunakan tab menu untuk memilih kategori pengaturan:
   - **Umum & Brand:** Mengubah Nama Brand & PT.
   - **Kontak & Operasional:** Mengubah Nomor Telepon, Nomor WhatsApp direct, Alamat, dan Jam Buka.
   - **SEO & Meta:** Mengubah Title & Description Google.
   - **Tracking & Pixel:** Mengisi ID GTM, GA4 Measurement ID, Meta Pixel ID, TikTok Pixel ID.
   - **Template WhatsApp:** Mengatur format pesan otomatis yang diterima di WA saat klien selesai mengisi form.
   - **Announcement Bar:** Mengaktifkan/menonaktifkan baris pengumuman paling atas.
3. Klik **Simpan Perubahan Settings**.

---

## 6. MENGELOLA AKUN SUPER ADMIN

1. Masuk ke menu **Pengguna (Admin)**.
2. Klik **+ Tambah Admin Baru** untuk menambahkan staf baru.
3. Isi Nama, Email, dan Password (minimal 8 karakter).
4. **Aturan Keamanan:**
   - Anda tidak dapat menonaktifkan atau menghapus akun Anda sendiri saat sedang login.
   - Sistem menjamin selalu ada minimal 1 akun Super Admin yang aktif.

---

*Jika mengalami kendala teknis, hubungi tim pengembang website.*
