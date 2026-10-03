<?php
/**
 * RBK Studio × RBK Konstruksi — Styleguide Visual & Verification Page
 * Access: Super Admin / Development Preview
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RBK Styleguide & Tokens Verification</title>
  
  <!-- Preconnect & Load Google Fonts identical to references -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;0,700;0,800;0,900;1,600;1,700;1,800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="/assets/css/styleguide.css">
</head>
<body>

<div class="styleguide-container">
  
  <header class="styleguide-header">
    <h1>RBK Design System & Styleguide</h1>
    <p>Visual Verification & Single Source of Truth — RBK Studio × RBK Konstruksi (Palet Terkunci 3.1)</p>
  </header>

  <!-- SECTION 1: COLOR SYSTEM -->
  <section class="sg-section">
    <h2>1. Palet Warna Terkunci & Token Turunan (Bagian 3.1)</h2>
    <div class="color-grid">
      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-black);"></div>
        <div class="color-info">
          <strong>--color-black</strong>
          <span>#0f0e0d</span>
          <small>Latar gelap, teks utama</small>
        </div>
      </div>
      
      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-white); border-bottom: 1px solid #eee;"></div>
        <div class="color-info">
          <strong>--color-white</strong>
          <span>#ffffff</span>
          <small>Latar utama terang</small>
        </div>
      </div>

      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-orange);"></div>
        <div class="color-info">
          <strong>--color-orange</strong>
          <span>#dd5c3e</span>
          <small>Aksen CTA utama, harga, badge</small>
        </div>
      </div>

      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-orange-strong);"></div>
        <div class="color-info">
          <strong>--color-orange-strong</strong>
          <span>#b44c34</span>
          <small>Hover oren & teks oren kecil</small>
        </div>
      </div>

      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-orange-soft); border-bottom: 1px solid #eee;"></div>
        <div class="color-info">
          <strong>--color-orange-soft</strong>
          <span>#fcf2f0</span>
          <small>Latar card Recommended</small>
        </div>
      </div>

      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-muted-light);"></div>
        <div class="color-info">
          <strong>--color-muted-light</strong>
          <span>#656564</span>
          <small>Teks sekunder di terang</small>
        </div>
      </div>

      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-muted-dark);"></div>
        <div class="color-info">
          <strong>--color-muted-dark</strong>
          <span>#b2b2b2</span>
          <small>Teks sekunder di gelap</small>
        </div>
      </div>

      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-surface-light); border-bottom: 1px solid #eee;"></div>
        <div class="color-info">
          <strong>--color-surface-light</strong>
          <span>#f5f5f5</span>
          <small>Section alternate & input bg</small>
        </div>
      </div>

      <div class="color-card">
        <div class="color-swatch" style="background: var(--color-surface-dark);"></div>
        <div class="color-info">
          <strong>--color-surface-dark</strong>
          <span>#1d1c1c</span>
          <small>Card di atas latar gelap</small>
        </div>
      </div>
    </div>

    <h3>Aturan Kontras (WCAG 2.1 Compliance)</h3>
    <table class="sg-table">
      <thead>
        <tr>
          <th>Kombinasi Warna</th>
          <th>Rasio</th>
          <th>Level WCAG</th>
          <th>Penggunaan</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Hitam di atas Putih</td>
          <td>19.28 : 1</td>
          <td><span class="pass-tag">AAA</span></td>
          <td>Teks utama & body paragraph</td>
        </tr>
        <tr>
          <td>Oren <code>#dd5c3e</code> di atas Hitam</td>
          <td>5.21 : 1</td>
          <td><span class="pass-tag">AA</span></td>
          <td>Heading gelap, <em>kata miring</em></td>
        </tr>
        <tr>
          <td>Oren <code>#dd5c3e</code> di atas Putih</td>
          <td>3.70 : 1</td>
          <td><span class="pass-tag">AA Large</span></td>
          <td>Hanya teks besar ≥24px, ikon, garis aksen</td>
        </tr>
        <tr>
          <td>Oren Strong <code>#b44c34</code> di atas Putih</td>
          <td>5.22 : 1</td>
          <td><span class="pass-tag">AA Normal</span></td>
          <td>Teks oren berukuran kecil (&lt;24px) di latar terang</td>
        </tr>
        <tr>
          <td>Label Hitam di atas Tombol Oren</td>
          <td>5.21 : 1</td>
          <td><span class="pass-tag">AA</span></td>
          <td>Default label tombol CTA oren utama</td>
        </tr>
      </tbody>
    </table>
  </section>

  <!-- SECTION 2: TYPOGRAPHY -->
  <section class="sg-section">
    <h2>2. Tipografi (Montserrat & Plus Jakarta Sans)</h2>
    <div style="display: flex; flex-direction: column; gap: 16px;">
      <div>
        <span style="font-size: 12px; color: var(--color-orange-strong); font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;">Eyebrow Label</span>
        <h1 style="font-family: var(--font-heading); font-size: var(--fs-h1); font-weight: 800; line-height: 1.05;">
          Bangun sekali. <em style="font-style: italic; color: var(--color-orange);">Rencanakan dengan benar sejak awal.</em>
        </h1>
      </div>

      <div>
        <h2 style="font-family: var(--font-heading); font-size: var(--fs-h2); font-weight: 800; border: none; padding: 0;">
          Jasa Arsitek & Kontraktor Rumah Terbaik di Bogor
        </h2>
      </div>

      <div>
        <h3 style="font-family: var(--font-heading); font-size: var(--fs-h3); font-weight: 700;">
          Paket Standard — Best Balance Between Design × Detail × Investment
        </h3>
      </div>

      <p style="font-size: var(--fs-body-lg); color: var(--color-muted-light);">
        Subjudul Paragraf: Rencanakan bersama RBK Studio, bangun bersama RBK Konstruksi. Satu tim, satu alur, dari konsep hingga serah terima.
      </p>

      <p style="font-size: var(--fs-body);">
        Teks Body Standar: Pengalaman kami sejak 2007 di grup properti memastikan setiap desain dapat direalisasikan dengan perhitungan struktur dan biaya yang presisi.
      </p>
    </div>
  </section>

  <!-- SECTION 3: BUTTONS -->
  <section class="sg-section">
    <h2>3. Gaya Tombol (Button Styles)</h2>
    <div class="btn-group">
      <a href="#" class="btn btn-orange">Konsultasi & Survei Gratis</a>
      <a href="#" class="btn btn-outline-dark">Lihat Paket Harga</a>
      <a href="#" class="btn btn-wa">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 2c-5.514 0-9.999 4.486-9.999 10 0 1.763.458 3.486 1.332 5.006l-1.364 4.994 5.118-1.342c1.464.798 3.12 1.219 4.913 1.219 5.514 0 9.999-4.486 9.999-10 0-5.514-4.485-10-9.999-10z"/></svg>
        Chat WhatsApp Directly
      </a>
    </div>

    <div style="background: var(--color-black); padding: 30px; border-radius: var(--radius-xl); margin-top: 20px;" class="btn-group">
      <a href="#" class="btn btn-orange">CTA di Latar Gelap</a>
      <a href="#" class="btn btn-outline-light">Outline di Latar Gelap</a>
    </div>
  </section>

  <!-- SECTION 4: CARDS & PACKAGES -->
  <section class="sg-section">
    <h2>4. Komponen Kartu (Cards & Tiers)</h2>
    <div class="card-grid">
      <div class="card">
        <span class="badge badge-black">Basic</span>
        <h3 style="font-size: 22px; margin: 12px 0 6px;">Paket Basic</h3>
        <p style="color: var(--color-muted-light); font-size: 14px;">Untuk kebutuhan konsep arsitektur dasar.</p>
        <div style="font-size: 28px; font-weight: 800; color: var(--color-black); margin: 16px 0;">Rp60.000 <small style="font-size: 14px; font-weight: 500;">/m²</small></div>
        <a href="#" class="btn btn-outline-dark" style="width: 100%;">Pilih Paket Basic</a>
      </div>

      <div class="card card-rec">
        <span class="badge badge-orange">Recommended</span>
        <h3 style="font-size: 22px; margin: 12px 0 6px;">Paket Standard</h3>
        <p style="color: var(--color-muted-light); font-size: 14px;">Best balance between design & budget.</p>
        <div style="font-size: 28px; font-weight: 800; color: var(--color-black); margin: 16px 0;">Rp80.000 <small style="font-size: 14px; font-weight: 500;">/m²</small></div>
        <a href="#" class="btn btn-orange" style="width: 100%;">Pilih Paket Standard</a>
      </div>

      <div class="card dark">
        <span class="badge badge-orange">Signature Service</span>
        <h3 style="font-size: 22px; margin: 12px 0 6px; color: #fff;">Paket Premium</h3>
        <p style="color: var(--color-muted-dark); font-size: 14px;">Proyek hunian mewah & komersial.</p>
        <div style="font-size: 28px; font-weight: 800; color: var(--color-orange); margin: 16px 0;">Rp150.000 <small style="font-size: 14px; font-weight: 500;">/m²</small></div>
        <a href="#" class="btn btn-outline-light" style="width: 100%;">Pilih Paket Premium</a>
      </div>
    </div>
  </section>

  <!-- SECTION 5: CHIPS, BADGES & STATUS (NO EXTRA COLORS) -->
  <section class="sg-section">
    <h2>5. Chip Filter & Badge Pipeline Status Admin (Aturan 8.2 — Tanpa Warna Tambahan)</h2>
    <div style="margin-bottom: 20px;">
      <p style="font-weight: 600; margin-bottom: 10px;">Chip Filter Portofolio:</p>
      <div class="chip-group">
        <button class="chip active">Semua</button>
        <button class="chip">Rumah</button>
        <button class="chip">Kost</button>
        <button class="chip">Ruko & Komersial</button>
        <button class="chip">Renovasi</button>
      </div>
    </div>

    <div>
      <p style="font-weight: 600; margin-bottom: 10px;">Pipeline Lead Status (Berdasarkan Aturan 8.2):</p>
      <div class="chip-group">
        <span class="badge badge-orange">Baru (Oren Penuh)</span>
        <span class="badge badge-outline">Dihubungi (Outline Hitam)</span>
        <span class="badge badge-outline">Survei Dijadwalkan</span>
        <span class="badge badge-black">Deal ✓ (Hitam Penuh)</span>
        <span class="badge badge-muted">Batal (Coret Abu)</span>
      </div>
    </div>
  </section>

  <!-- SECTION 6: FORM ELEMENTS -->
  <section class="sg-section">
    <h2>6. Elemen Form (Input & Select)</h2>
    <div style="max-width: 500px; background: var(--color-surface-light); padding: 24px; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light);">
      <div class="form-group">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" class="form-control" placeholder="Masukkan nama Anda">
      </div>

      <div class="form-group">
        <label for="wa">Nomor WhatsApp</label>
        <input type="text" id="wa" class="form-control" placeholder="0812...">
      </div>

      <div class="form-group">
        <label for="kebutuhan">Kebutuhan Layanan</label>
        <select id="kebutuhan" class="form-control">
          <option>Desain + Bangun (RBK Design & Build)</option>
          <option>Desain saja (RBK Studio)</option>
          <option>Bangun saja (RBK Konstruksi)</option>
          <option>Renovasi (RenoVancy)</option>
        </select>
      </div>

      <button class="btn btn-orange" style="width: 100%;">Kirim & Konsultasi Gratis</button>
    </div>
  </section>

</div>

</body>
</html>
