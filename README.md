# LANDING PAGE RBK (RBK STUDIO × RBK KONSTRUKSI) + DASHBOARD MANAJEMEN CRUD

Satu halaman landing page konversi tinggi untuk **Rancang Bangun Kreasi (RBK)** yang menggabungkan jasa arsitek (**RBK Studio**) dan kontraktor (**RBK Konstruksi**), dilengkapi dengan **Dashboard Admin CRUD** berbasis **PHP 8.2+ Native** dan **MySQL 8.0**.

---

## 1. PERSYARATAN SISTEM (SYSTEM REQUIREMENTS)

- **PHP:** Version 8.2 atau lebih baru (Extension wajib: `pdo_mysql`, `gd`, `json`, `mbstring`, `fileinfo`, `openssl`).
- **Database:** MySQL 8.0+ atau MariaDB 10.6+ (InnoDB, `utf8mb4_unicode_ci`).
- **Web Server:** Apache 2.4+ (`mod_rewrite` & `mod_headers` aktif) atau Nginx 1.20+.
- **Composer:** Version 2.0+.

---

## 2. PANDUAN INSTALASI LOKAL (LOCAL INSTALLATION)

1. **Clone / Download Repository:**
   Letakkan file proyek pada folder web root Anda (misal `/Users/firdaus/Downloads/rbkstudio2`).

2. **Install Dependensi Composer:**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`):**
   Salin `.env.example` menjadi `.env` dan atur kredensial database Anda:
   ```env
   APP_URL="http://localhost:8000"
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=rbk_db
   DB_USER=root
   DB_PASS=
   INITIAL_ADMIN_EMAIL=admin@rancangbangunkreasi.id
   ```

4. **Jalankan Skrip Migration & Seed Otomatis:**
   ```bash
   php scripts/install.php
   ```
   *Skrip ini otomatis membuat database `rbk_db`, mengeksekusi `schema.sql`, mengisikan `seed.sql`, dan mencetak Password Super Admin Awal.*

5. **Jalankan Server Lokal PHP:**
   ```bash
   php -S localhost:8000 -t public
   ```
   Akses publik di `http://localhost:8000` dan dashboard admin di `http://localhost:8000/admin`.

---

## 3. PANDUAN DEPLOYMENT PRODUKSI

### A. Shared Hosting cPanel (Apache)

1. Upload seluruh folder proyek ke root hosting (di luar `public_html`).
2. Pindahkan seluruh isi folder `/public` ke dalam folder `public_html` hosting Anda.
3. Edit `public_html/index.php` dan sesuaikan path autoload:
   ```php
   require_once __DIR__ . '/../vendor/autoload.php';
   ```
4. Buat database MySQL pada cPanel, import `database/schema.sql` dan `database/seed.sql`.
5. Sesuaikan variabel `.env` dengan kredensial database cPanel.

### B. VPS Linux (Nginx)

Konfigurasi Block Server Nginx dengan Document Root mengarah ke `/public`:

```nginx
server {
    listen 80;
    server_name rancangbangunkreasi.id www.rancangbangunkreasi.id;
    root /var/www/rbkstudio2/public;
    index index.php index.html;

    charset utf-8;

    # Security Headers
    add_header X-Frame-Options "DENY";
    add_header X-Content-Type-Options "nosniff";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Block access to hidden files
    location ~ /\. {
        deny all;
    }

    # Block PHP execution in uploads directory
    location ~* ^/uploads/.*\.php$ {
        deny all;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## 4. KONFIGURASI CRON JOB BACKUP OTOMATIS

Atur Cron Job harian pada server (pukul 02.00 pagi) untuk menjalankan backup database dan rotasi 14 hari:

```bash
0 2 * * * php /var/www/rbkstudio2/scripts/backup.php > /dev/null 2>&1
```

---

## 5. DOKUMEN LAINNYA

- **[docs/design-tokens.md](file:///Users/firdaus/Downloads/rbkstudio2/docs/design-tokens.md):** Acuan resmi sistem warna terkunci 3.1 & tipografi.
- **[docs/IMPLEMENTATION-PLAN.md](file:///Users/firdaus/Downloads/rbkstudio2/docs/IMPLEMENTATION-PLAN.md):** Rencana implementasi teknis detail.
- **[docs/USER-GUIDE-ADMIN.md](file:///Users/firdaus/Downloads/rbkstudio2/docs/USER-GUIDE-ADMIN.md):** Panduan operasional pengguna dashboard untuk staf admin.
- **[docs/FASE-7-REPORT.md](file:///Users/firdaus/Downloads/rbkstudio2/docs/FASE-7-REPORT.md):** Laporan QA penuh dan checklist Definition of Done.
