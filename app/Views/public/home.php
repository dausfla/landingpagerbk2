<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?= e($settings['meta_title'] ?? 'Jasa Arsitek & Kontraktor Rumah Bogor | RBK Studio & RBK Konstruksi') ?></title>
  <meta name="description" content="<?= e($settings['meta_description'] ?? '') ?>">
  <link rel="canonical" href="<?= e(env('APP_URL', 'http://localhost:8000')) ?>">
  
  <!-- Open Graph -->
  <meta property="og:title" content="<?= e($settings['meta_title'] ?? '') ?>">
  <meta property="og:description" content="<?= e($settings['meta_description'] ?? '') ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= e(env('APP_URL', 'http://localhost:8000')) ?>">

  <!-- Preconnect & Load Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,600;0,700;0,800;0,900;1,600;1,700;1,800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Preload Hero LCP Image -->
  <link rel="preload" as="image" href="/assets/img/la-bella.webp" fetchpriority="high">

  <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
  
  <!-- Embedded JSON-LD Schemas -->
  <?= $jsonLdSchemas ?>
</head>
<body>

  <!-- Dynamic Pricing Data for JS Calculator -->
  <script type="application/json" id="pricing-data"><?= $pricingDataJson ?></script>

  <!-- S0. ANNOUNCEMENT BAR -->
  <?php if (isset($sections['s0_announcement']) && ($settings['announcement_enabled'] ?? '1') === '1'): ?>
    <div class="announcement-bar">
      <?= e($sections['s0_announcement']['title'] ?? '') ?>
    </div>
  <?php endif; ?>

  <!-- S1. NAVBAR STICKY -->
  <?php if (isset($sections['s1_navbar'])): ?>
    <header class="navbar-sticky">
      <div class="container navbar-inner">
        <a href="#top" class="navbar-brand" aria-label="Rancang Bangun Kreasi (RBK)">
          <img src="/assets/img/logo-rbk.png" alt="Rancang Bangun Kreasi Logo" class="brand-logo" width="200" height="48">
        </a>

        <button class="hamburger-toggle" aria-label="Toggle Navigation">☰</button>

        <ul class="navbar-menu">
          <li><a href="#layanan">Layanan</a></li>
          <li><a href="#portofolio">Portofolio</a></li>
          <li><a href="#harga">Harga</a></li>
          <li><a href="#proses">Proses</a></li>
          <li><a href="#faq">FAQ</a></li>
          <li>
            <a href="https://wa.me/<?= e($settings['whatsapp_number'] ?? '6281234593742') ?>" target="_blank" class="btn btn-orange btn-wa-nav" style="padding: 10px 20px; font-size: 13.5px; gap: 8px;">
              <img src="/assets/img/whatsapp-icon.png" alt="WhatsApp" width="22" height="22" style="width: 22px; height: 22px; object-fit: contain; flex-shrink: 0;">
              <span>Konsultasi Gratis</span>
            </a>
          </li>
        </ul>
      </div>
    </header>
  <?php endif; ?>

  <!-- S2. HERO SECTION -->
  <?php if (isset($sections['s2_hero'])): ?>
    <section id="top" class="hero-section dark">
      <div class="container hero-grid">
        <div class="hero-content reveal">
          <span class="eyebrow"><?= e($sections['s2_hero']['eyebrow']) ?></span>
          <h1><?= $sections['s2_hero']['title'] ?></h1>
          <p style="font-size: clamp(18px, 2.2vw, 20px); line-height: 1.65; margin-bottom: var(--space-6);"><?= $sections['s2_hero']['subtitle'] ?></p>

          <div class="hero-chips">
            <span class="hero-chip highlight">Desain mulai <?= format_rupiah(\App\Models\PackageModel::getMinPrice('desain')) ?>/m²</span>
            <span class="hero-chip highlight">Bangun mulai <?= format_rupiah(\App\Models\PackageModel::getMinPrice('bangun')) ?>/m²</span>
            <span class="hero-chip">Konsultasi & Survei Gratis</span>
          </div>

          <div class="hero-chips" style="margin-bottom: var(--space-8);">
            <span class="hero-chip" style="font-size: 13px; padding: 8px 18px;">Area: <strong style="color: var(--color-orange);">Jakarta · Bogor · Depok · Tangerang · Bekasi</strong></span>
          </div>

          <div style="display: flex; gap: 14px; flex-wrap: wrap;">
            <a href="#konsultasi" class="btn btn-orange" style="padding: 16px 28px; font-size: 16px;">Konsultasi & Survei Gratis</a>
            <a href="#harga" class="btn btn-outline-light" style="padding: 16px 28px; font-size: 16px;">Lihat Paket Harga</a>
          </div>
        </div>

        <figure class="hero-visual reveal">
          <picture>
            <source srcset="/assets/img/la-bella.webp" type="image/webp">
            <img src="/assets/img/la-bella.webp" alt="La Bella Office & Warehouse karya RBK" width="800" height="500" fetchpriority="high">
          </picture>
          <figcaption>Proyek Komersial: La Bella Office & Warehouse, Bogor (Design & Build oleh RBK)</figcaption>
        </figure>
      </div>
    </section>
  <?php endif; ?>

  <!-- S3. TRUST STRIP -->
  <?php if (isset($sections['s3_trust'])): ?>
    <section class="section alt">
      <div class="container">
        <?php if (!empty($sections['s3_trust']['title']) || !empty($sections['s3_trust']['eyebrow'])): ?>
          <div style="text-align: center; margin-bottom: var(--space-8);" class="reveal">
            <?php if (!empty($sections['s3_trust']['eyebrow'])): ?>
              <span class="eyebrow"><?= e($sections['s3_trust']['eyebrow']) ?></span>
            <?php endif; ?>
            <?php if (!empty($sections['s3_trust']['title'])): ?>
              <h2 style="font-size: var(--fs-h2);"><?= $sections['s3_trust']['title'] ?></h2>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <div class="trust-grid reveal">
          <?php foreach ($stats as $st): ?>
            <div class="card trust-card">
              <div class="val"><?= e($st['value']) ?></div>
              <div class="lbl"><?= e($st['label']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S4. MASALAH (KENAPA PERLU DENGAN BENAR) -->
  <?php if (isset($sections['s4_problem'])): ?>
    <section id="kenapa" class="section">
      <div class="container reveal" style="text-align: center;">
        <span class="eyebrow"><?= e($sections['s4_problem']['eyebrow']) ?></span>
        <h2 style="font-size: var(--fs-h2); margin-bottom: var(--space-4);"><?= $sections['s4_problem']['title'] ?></h2>
        <p style="font-size: var(--fs-body-lg); color: var(--color-muted-light); margin-bottom: var(--space-8);"><?= e($sections['s4_problem']['subtitle']) ?></p>

        <div class="problem-grid">
          <div class="problem-card">
            <div class="problem-body">
              <strong>❌ Layout Kurang Optimal</strong>
              <span>Ruang terasa sempit, gelap, dan sirkulasi udara tidak lancar.</span>
            </div>
          </div>

          <div class="problem-card">
            <div class="problem-body">
              <strong>❌ Perubahan Instalasi</strong>
              <span>Bongkar pasang pipa dan titik listrik di tengah proses konstruksi.</span>
            </div>
          </div>

          <div class="problem-card">
            <div class="problem-body">
              <strong>❌ Bongkar Ulang Bangunan</strong>
              <span>Akibat hasil fisik tidak sesuai harapan atau kesalahan struktur.</span>
            </div>
          </div>

          <div class="problem-card">
            <div class="problem-body">
              <strong>❌ Pembengkakan Anggaran</strong>
              <span>Biaya tak terduga yang melambung tinggi tanpa kontrol RAB.</span>
            </div>
          </div>
        </div>

        <a href="#konsultasi" class="btn btn-orange" style="padding: 16px 32px; font-size: 16px;">Hindari Masalah di Atas — Konsultasi Sekarang</a>
      </div>
    </section>
  <?php endif; ?>

  <!-- S5. DUA JALUR (CORE INTIE SECTION) -->
  <?php if (isset($sections['s5_two_paths'])): ?>
    <section id="layanan" class="section alt">
      <div class="container">
        <div style="text-align: center; margin-bottom: var(--space-10);" class="reveal">
          <span class="eyebrow"><?= e($sections['s5_two_paths']['eyebrow']) ?></span>
          <h2 style="font-size: var(--fs-h2);"><?= $sections['s5_two_paths']['title'] ?></h2>
          <p style="color: var(--color-muted-light); font-size: var(--fs-body-lg);"><?= e($sections['s5_two_paths']['subtitle']) ?></p>
        </div>

        <div class="two-paths-grid reveal">
          <!-- Kartu 1: RBK Studio (PLAN) -->
          <div class="path-card studio">
            <span class="badge badge-black" style="align-self: flex-start;">PLAN FIRST</span>
            <h3 style="font-size: 24px; margin-top: 12px;">RBK Studio — Jasa Arsitek</h3>
            <p style="color: var(--color-muted-light); font-size: 14px;">Perencanaan arsitektur presisi & sadar biaya.</p>
            
            <div class="price-tag">Mulai <?= format_rupiah(\App\Models\PackageModel::getMinPrice('desain')) ?> <small style="font-size: 14px; font-weight: 500;">/m²</small></div>
            
            <ul>
              <li>Konsep Arsitektur & Space Planning</li>
              <li>Visualisasi 3D Exterior & Interior Presisi</li>
              <li>Dokumen Gambar Kerja Detail (DED)</li>
              <li>Perhitungan Struktur & MEP Lengkap</li>
              <li>Rencana Anggaran Biaya (RAB) Realistis</li>
            </ul>

            <a href="#konsultasi" class="btn btn-outline-dark" style="width: 100%;">Konsultasi Desain</a>
          </div>

          <!-- Kartu 2: RBK Konstruksi (BUILD) -->
          <div class="path-card konstruksi">
            <span class="badge badge-orange" style="align-self: flex-start;">BUILD ONCE</span>
            <h3 style="font-size: 24px; margin-top: 12px;">RBK Konstruksi — Jasa Bangun</h3>
            <p style="color: var(--color-muted-light); font-size: 14px;">Pembangunan fisik bergaransi & pengawasan ketat.</p>
            
            <div class="price-tag">Mulai <?= format_rupiah(\App\Models\PackageModel::getMinPrice('bangun')) ?> <small style="font-size: 14px; font-weight: 500;">/m²</small></div>
            
            <ul>
              <li>Pembangunan fisik sesuai gambar desain</li>
              <li>Paket material dengan merek & spesifikasi jelas</li>
              <li>Tukang spesialis & pengawas lapangan berpengalaman</li>
              <li>RAB detail ditetapkan setelah survei lokasi</li>
              <li>Garansi pemeliharaan pasca serah terima</li>
            </ul>

            <a href="#konsultasi" class="btn btn-orange" style="width: 100%;">Konsultasi Bangun</a>
          </div>
        </div>

        <!-- Single Integrated Design & Build Bar -->
        <div style="background: var(--color-black); color: var(--color-white); border-radius: var(--radius-xl); padding: 24px; margin-top: var(--space-8); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;" class="reveal">
          <div>
            <span class="badge badge-orange">Design & Build</span>
            <h4 style="font-size: 18px; color: var(--color-white); margin-top: 6px;">Ambil Keduanya dalam Satu Alur</h4>
            <p style="color: var(--color-muted-dark); font-size: 14px; margin: 0;">Dokumen desain RBK Studio langsung diteruskan ke tim RBK Konstruksi tanpa jeda dan tanpa salah komunikasi.</p>
          </div>
          <a href="#konsultasi" class="btn btn-orange" style="white-space: nowrap;">Konsultasi Design & Build</a>
        </div>

        <!-- Small Sub-brands: RenoVancy & RBK Kreasi -->
        <div style="display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap;" class="reveal">
          <div style="flex: 1; min-width: 280px; background: var(--color-white); padding: 16px 20px; border-radius: var(--radius-lg); border: 1px solid var(--color-border-light);">
            <strong>RenoVancy (Renovasi Total):</strong> <span style="font-size: 13.5px; color: var(--color-muted-light);">Harga ditentukan setelah survei lokasi (TANPA angka hardcode).</span>
          </div>
          <div style="flex: 1; min-width: 280px; background: var(--color-white); padding: 16px 20px; border-radius: var(--radius-lg); border: 1px solid var(--color-border-light);">
            <strong>RBK Kreasi (Interior & Taman):</strong> <span style="font-size: 13.5px; color: var(--color-muted-light);">Desain & pengerjaan interior custom serta lanskap tropis.</span>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S6. LINGKUP RBK STUDIO -->
  <?php if (isset($sections['s6_studio_scope'])): ?>
    <section class="section">
      <div class="container">
        <div style="text-align: center; margin-bottom: var(--space-8);" class="reveal">
          <span class="eyebrow"><?= e($sections['s6_studio_scope']['eyebrow']) ?></span>
          <h2 style="font-size: var(--fs-h2);"><?= $sections['s6_studio_scope']['title'] ?></h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;" class="reveal">
          <div style="background: var(--color-surface-light); padding: 24px; border-radius: var(--radius-lg);">
            <h3>1. Konsep Arsitektur</h3>
            <p style="font-size: 14px; color: var(--color-muted-light); margin-top: 8px;">Studi bentuk, gaya tropis modern, dan penyesuaian lahan.</p>
          </div>
          <div style="background: var(--color-surface-light); padding: 24px; border-radius: var(--radius-lg);">
            <h3>2. Space Planning</h3>
            <p style="font-size: 14px; color: var(--color-muted-light); margin-top: 8px;">Zoning tata ruang efisien dan sirkulasi udara optimal.</p>
          </div>
          <div style="background: var(--color-surface-light); padding: 24px; border-radius: var(--radius-lg);">
            <h3>3. Visualisasi 3D</h3>
            <p style="font-size: 14px; color: var(--color-muted-light); margin-top: 8px;">Render 3D eksterior presisi tinggi memberikan gambaran nyata.</p>
          </div>
          <div style="background: var(--color-surface-light); padding: 24px; border-radius: var(--radius-lg);">
            <h3>4. Gambar Kerja (DED)</h3>
            <p style="font-size: 14px; color: var(--color-muted-light); margin-top: 8px;">Dokumen teknis acuan lengkap untuk tukang di lapangan.</p>
          </div>
          <div style="background: var(--color-surface-light); padding: 24px; border-radius: var(--radius-lg);">
            <h3>5. Struktur & MEP</h3>
            <p style="font-size: 14px; color: var(--color-muted-light); margin-top: 8px;">Perhitungan pembesian beton bertulang, listrik, dan air.</p>
          </div>
          <div style="background: var(--color-surface-light); padding: 24px; border-radius: var(--radius-lg);">
            <h3>6. RAB Detail</h3>
            <p style="font-size: 14px; color: var(--color-muted-light); margin-top: 8px;">Rencana anggaran biaya transparan per item pekerjaan.</p>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S7. DESAIN TROPIS BOGOR -->
  <?php if (isset($sections['s7_tropical_bogor'])): ?>
    <section class="section alt">
      <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-8); align-items: center;" class="reveal">
          <div>
            <span class="eyebrow"><?= e($sections['s7_tropical_bogor']['eyebrow']) ?></span>
            <h2 style="font-size: var(--fs-h2); margin-bottom: 16px;"><?= $sections['s7_tropical_bogor']['title'] ?></h2>
            <p style="color: var(--color-muted-light); font-size: 15px; margin-bottom: 24px;"><?= e($sections['s7_tropical_bogor']['subtitle']) ?></p>

            <ul style="list-style: none; display: flex; flex-direction: column; gap: 12px;">
              <li style="font-size: 14.5px;">✔️ Kemiringan atap & talang khusus untuk curah hujan tinggi Kota Bogor</li>
              <li style="font-size: 14.5px;">✔️ Teritisan atap lebar melindungi dinding dari tampias hujan</li>
              <li style="font-size: 14.5px;">✔️ Ventilasi silang (cross ventilation) menjaga kelembaban udara</li>
              <li style="font-size: 14.5px;">✔️ Pencahayaan alami maksimal tanpa membuat ruangan panas</li>
              <li style="font-size: 14.5px;">✔️ Peninggian pelevelan lantai & saluran air terintegrasi</li>
            </ul>
          </div>

          <div>
            <img src="/assets/img/arsya.webp" alt="Desain rumah tropis Bogor" style="width: 100%; border-radius: var(--radius-xl); box-shadow: var(--shadow-md);" loading="lazy" width="600" height="400">
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S8. PORTOFOLIO (BEFORE/AFTER + CAROUSEL + FILTER) -->
  <?php if (isset($sections['s8_portfolio'])): ?>
    <section id="portofolio" class="section">
      <div class="container">
        <div style="text-align: center; margin-bottom: var(--space-8);" class="reveal">
          <span class="eyebrow"><?= e($sections['s8_portfolio']['eyebrow']) ?></span>
          <h2 style="font-size: var(--fs-h2);"><?= $sections['s8_portfolio']['title'] ?></h2>
        </div>

        <!-- Before/After Slider Sample -->
        <div class="ba-container reveal" style="--pos: 50%;">
          <img src="/assets/img/rumah-a-after.webp" alt="Sesudah Renovasi" class="ba-img ba-after" loading="lazy" width="960" height="600">
          <img src="/assets/img/rumah-a-before.webp" alt="Sebelum Renovasi" class="ba-img ba-before" loading="lazy" width="960" height="600">
          <div class="ba-divider"></div>
          <div class="ba-handle">↔</div>
          <span class="ba-label ba-label-before">Sebelum</span>
          <span class="ba-label ba-label-after">Sesudah</span>
          <input type="range" min="0" max="100" value="50" class="ba-slider-input" aria-label="Geser perbandingan sebelum dan sesudah">
        </div>

        <!-- Category Filter Chips -->
        <div class="chip-group reveal" style="justify-content: center; margin-bottom: var(--space-8);">
          <button class="chip portfolio-chip active" data-category="all">Semua Proyek</button>
          <?php foreach ($categories as $cat): ?>
            <button class="chip portfolio-chip" data-category="<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></button>
          <?php endforeach; ?>
        </div>

        <!-- Portfolio Grid (Every Card Has Its Own Before/After Slider) -->
        <div class="portfolio-grid reveal">
          <?php foreach ($portfolios as $p): ?>
            <div class="card portfolio-card" data-category="<?= e($p['category_slug']) ?>">
              <!-- Interactive Before/After Slider inside Each Card -->
              <div class="ba-container card-ba-slider" style="--pos: 50%;">
                <?php
                  $afterSrc = !empty($p['after_image']) ? e($p['after_image']) : '/assets/img/' . e($p['slug']) . '.webp';
                  $beforeSrc = !empty($p['before_image']) ? e($p['before_image']) : '/assets/img/' . e($p['slug']) . '-before.webp';
                ?>
                <img src="<?= $afterSrc ?>" alt="Sesudah <?= e($p['title']) ?>" class="ba-img ba-after" loading="lazy" width="400" height="250">
                <img src="<?= $beforeSrc ?>" alt="Sebelum <?= e($p['title']) ?>" class="ba-img ba-before" loading="lazy" width="400" height="250">
                <div class="ba-divider"></div>
                <div class="ba-handle">↔</div>
                <span class="ba-label ba-label-before">Sebelum</span>
                <span class="ba-label ba-label-after">Sesudah</span>
                <input type="range" min="0" max="100" value="50" class="ba-slider-input" aria-label="Geser perbandingan sebelum dan sesudah <?= e($p['title']) ?>">
              </div>

              <!-- Card Info & Category Badge -->
              <div class="portfolio-card-info">
                <span class="portfolio-category-badge">
                  <span class="badge-dot"></span><?= e($p['category_name']) ?>
                </span>
                <h3 style="font-size: 18px; margin: 10px 0 4px; font-weight: 800; color: var(--color-black);"><?= e($p['title']) ?></h3>
                <p style="font-size: 13.5px; color: var(--color-muted-light); margin: 0; font-weight: 500;"><?= e($p['location']) ?> · <?= e($p['year']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Portfolio CTA Action Button -->
        <div style="text-align: center; margin-top: 40px;" class="reveal">
          <a href="https://rancangbangunkreasi.id/projects/" target="_blank" rel="noopener" class="btn btn-orange" style="padding: 14px 32px; font-size: 15px; gap: 8px; font-weight: 700;">
            <span>Jelajahi Portofolio Kami</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </a>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S9. KEUNGGULAN (8 CURATED VALUES) -->
  <?php if (isset($sections['s9_advantages'])): ?>
    <section id="keunggulan" class="section alt">
      <div class="container">
        <div style="text-align: center; margin-bottom: var(--space-8);" class="reveal">
          <span class="eyebrow"><?= e($sections['s9_advantages']['eyebrow']) ?></span>
          <h2 style="font-size: var(--fs-h2);"><?= $sections['s9_advantages']['title'] ?></h2>
        </div>

        <div class="advantages-grid reveal">
          <?php foreach ($advantages as $idx => $adv): ?>
            <div class="adv-card">
              <div class="adv-card-header">
                <span class="adv-num"><?= sprintf('%02d', $idx + 1) ?></span>
              </div>
              <h3><?= e($adv['title']) ?></h3>
              <p><?= e($adv['description']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S10. HARGA & TABEL SPESIFIKASI -->
  <?php if (isset($sections['s10_pricing'])): ?>
    <section id="harga" class="section">
      <div class="container">
        <div style="text-align: center; margin-bottom: var(--space-8);" class="reveal">
          <span class="eyebrow"><?= e($sections['s10_pricing']['eyebrow']) ?></span>
          <h2 style="font-size: var(--fs-h2);"><?= $sections['s10_pricing']['title'] ?></h2>
          <p style="color: var(--color-muted-light); font-size: var(--fs-body-lg);"><?= e($sections['s10_pricing']['subtitle']) ?></p>
        </div>

        <!-- Package Grid -->
        <div class="pricing-grid reveal">
          <?php foreach ($packages as $pkg): ?>
            <div class="package-card <?= $pkg['badge'] === 'Recommended' || $pkg['badge'] === 'Paling seimbang' ? 'recommended' : '' ?>">
              <?php if (!empty($pkg['badge'])): ?>
                <span class="badge badge-orange" style="align-self: flex-start; margin-bottom: 12px;"><?= e($pkg['badge']) ?></span>
              <?php else: ?>
                <span class="badge badge-black" style="align-self: flex-start; margin-bottom: 12px;"><?= e(ucfirst($pkg['service_type'])) ?></span>
              <?php endif; ?>

              <h3 style="font-size: 22px; margin-bottom: 6px;"><?= e($pkg['name']) ?></h3>
              <p style="font-size: 13px; color: var(--color-muted-light); min-height: 40px;"><?= e($pkg['tagline']) ?></p>

              <div style="font-size: 26px; font-weight: 800; margin: 16px 0;">
                <?= format_rupiah_compact($pkg['price_min'], $pkg['price_max']) ?>
                <small style="font-size: 13px; font-weight: 500;"><?= e($pkg['unit']) ?></small>
              </div>

              <?php if (!empty($pkg['specs'])): ?>
                <table class="sg-table" style="font-size: 12.5px; margin-bottom: 20px;">
                  <?php foreach ($pkg['specs'] as $sp): ?>
                    <tr>
                      <td style="font-weight: 700; width: 35%;"><?= e($sp['label']) ?></td>
                      <td><?= e($sp['value']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </table>
              <?php endif; ?>

              <a href="#konsultasi" class="btn btn-orange" style="margin-top: auto; width: 100%;"><?= e($pkg['cta_label']) ?></a>
            </div>
          <?php endforeach; ?>
        </div>

        <p style="font-size: 13px; color: var(--color-muted-light); text-align: center; margin-top: var(--space-6);" class="reveal">
          *Harga paket belum termasuk bangunan pendukung (pagar, carport, lanskap). Angka final ditetapkan setelah survei lokasi dan penyusunan RAB.
        </p>
      </div>
    </section>
  <?php endif; ?>

  <!-- S11. KALKULATOR ESTIMASI BIAYA -->
  <?php if (isset($sections['s11_calculator'])): ?>
    <section id="kalkulator" class="section alt">
      <div class="container">
        <div class="calc-box reveal">
          <div style="text-align: center; margin-bottom: var(--space-8);">
            <span class="badge badge-orange" style="margin-bottom: 8px;">Interactive Tool</span>
            <h2 style="font-size: var(--fs-h2); color: var(--color-white);"><?= $sections['s11_calculator']['title'] ?></h2>
            <p style="color: var(--color-muted-dark); font-size: 15px;"><?= e($sections['s11_calculator']['subtitle']) ?></p>
          </div>

          <div class="calc-two-cards-grid">
            <!-- CARD 1: DESAIN (RBK STUDIO) -->
            <div class="calc-card">
              <div class="calc-card-header">
                <span class="badge badge-orange" style="font-size: 11px; padding: 4px 10px;">RBK Studio</span>
                <h3>1. Estimasi Desain Arsitektur</h3>
                <p>Kalkulasi kebutuhan perencanaan & gambar kerja</p>
              </div>

              <div class="calc-card-body">
                <div class="form-group">
                  <label style="color: var(--color-white); display: flex; justify-content: space-between; align-items: center;">
                    <span>Luas Bangunan (m²)</span>
                    <input type="number" id="calc-desain-num" value="120" min="30" max="1000" class="calc-num-input">
                  </label>
                  <input type="range" id="calc-desain-slider" min="30" max="1000" step="5" value="120" class="calc-range-slider">
                </div>

                <div class="form-group" style="margin-top: 20px;">
                  <label style="color: var(--color-white);">Pilih Paket Desain</label>
                  <select id="calc-select-desain" class="form-control calc-select">
                    <option value="Basic">Basic (Rp60.000/m²)</option>
                    <option value="Standard" selected>Standard (Rp80.000/m²)</option>
                    <option value="Premium">Premium (Rp150.000/m²)</option>
                  </select>
                </div>

                <div class="calc-result-box" style="margin-top: 24px;">
                  <span style="font-size: 13px; color: var(--color-muted-dark);">Perkiraan Biaya Desain:</span>
                  <div id="calc-desain-result" class="big-val">Rp9,6 jt</div>
                  <div id="calc-desain-formula" style="font-size: 13px; color: var(--color-muted-dark); margin-top: 6px;">120 m² × Rp80.000/m² (Standard)</div>
                  
                  <small style="display: block; font-size: 11px; color: var(--color-muted-dark); margin-top: 12px;">
                    *Sudah termasuk Konsep, 3D Render & GAMBAR KERJA lengkap.
                  </small>
                </div>
              </div>

              <div class="calc-card-footer" style="margin-top: 24px;">
                <button id="btn-calc-desain" class="btn btn-orange" style="width: 100%;">Konsultasikan Desain Ini</button>
              </div>
            </div>

            <!-- CARD 2: PEMBANGUNAN (RBK KONSTRUKSI) -->
            <div class="calc-card">
              <div class="calc-card-header">
                <span class="badge badge-orange" style="font-size: 11px; padding: 4px 10px;">RBK Konstruksi</span>
                <h3>2. Estimasi Pembangunan Fisik</h3>
                <p>Kalkulasi estimasi pekerjaan struktur & material</p>
              </div>

              <div class="calc-card-body">
                <div class="form-group">
                  <label style="color: var(--color-white); display: flex; justify-content: space-between; align-items: center;">
                    <span>Luas Bangunan (m²)</span>
                    <input type="number" id="calc-bangun-num" value="120" min="30" max="1000" class="calc-num-input">
                  </label>
                  <input type="range" id="calc-bangun-slider" min="30" max="1000" step="5" value="120" class="calc-range-slider">
                </div>

                <div class="form-group" style="margin-top: 20px;">
                  <label style="color: var(--color-white);">Pilih Paket Bangun</label>
                  <select id="calc-select-bangun" class="form-control calc-select">
                    <option value="Basic">Basic (Rp4,0–4,5 jt/m²)</option>
                    <option value="Standard" selected>Standard (Rp4,5–5,0 jt/m²)</option>
                    <option value="Premium">Premium (Rp6,0–7,5 jt/m²)</option>
                  </select>
                </div>

                <div class="calc-result-box" style="margin-top: 24px;">
                  <span style="font-size: 13px; color: var(--color-muted-dark);">Perkiraan Biaya Pembangunan:</span>
                  <div id="calc-bangun-result" class="big-val">Rp540–600 jt</div>
                  <div id="calc-bangun-formula" style="font-size: 13px; color: var(--color-muted-dark); margin-top: 6px;">120 m² × Rp4,5–5 jt/m² (Standard)</div>
                  
                  <small style="display: block; font-size: 11px; color: var(--color-muted-dark); margin-top: 12px;">
                    *Estimasi awal fisik, penawaran resmi mengacu RAB final.
                  </small>
                </div>
              </div>

              <div class="calc-card-footer" style="margin-top: 24px;">
                <button id="btn-calc-bangun" class="btn btn-orange" style="width: 100%;">Konsultasikan Pembangunan Ini</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S12. ALUR KERJA (8 LANGKAH TIMELINE) -->
  <?php if (isset($sections['s12_process'])): ?>
    <section id="proses" class="section">
      <div class="container">
        <div style="text-align: center; margin-bottom: var(--space-8);" class="reveal">
          <span class="eyebrow"><?= e($sections['s12_process']['eyebrow']) ?></span>
          <h2 style="font-size: var(--fs-h2);"><?= $sections['s12_process']['title'] ?></h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;" class="reveal">
          <?php foreach ($processSteps as $step): ?>
            <div style="background: var(--color-surface-light); padding: 20px; border-radius: var(--radius-lg); position: relative;">
              <span class="badge badge-black" style="margin-bottom: 8px;">Langkah <?= $step['step_no'] ?></span>
              <h3 style="font-size: 16px; margin-bottom: 6px;"><?= e($step['title']) ?></h3>
              <p style="font-size: 13px; color: var(--color-muted-light);"><?= e($step['description']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S15. CTA BAND -->
  <?php if (isset($sections['s15_cta_band'])): ?>
    <section class="section" style="background: var(--color-orange); color: var(--color-black);">
      <div class="container text-center reveal" style="text-align: center;">
        <h2 style="font-size: clamp(28px, 4.5vw, 48px); font-weight: 900; margin-bottom: 12px;">PLAN FIRST. BUILD ONCE.</h2>
        <p style="font-size: var(--fs-body-lg); font-weight: 600; margin-bottom: 24px;"><?= e($sections['s15_cta_band']['subtitle']) ?></p>
        <a href="#konsultasi" class="btn btn-outline-dark" style="background: var(--color-black); color: var(--color-white); border-color: var(--color-black);">Konsultasi & Survei Gratis Sekarang</a>
      </div>
    </section>
  <?php endif; ?>

  <!-- S16. FORM KONSULTASI 2-LANGKAH -->
  <?php if (isset($sections['s16_lead_form'])): ?>
    <section id="konsultasi" class="section">
      <div class="container container-narrow">
        <div style="text-align: center; margin-bottom: var(--space-8);" class="reveal">
          <span class="eyebrow"><?= e($sections['s16_lead_form']['eyebrow']) ?></span>
          <h2 style="font-size: var(--fs-h2);"><?= $sections['s16_lead_form']['title'] ?></h2>
          <p style="color: var(--color-muted-light); font-size: var(--fs-body-lg);"><?= e($sections['s16_lead_form']['subtitle']) ?></p>
        </div>

        <div style="background: var(--color-white); padding: 32px; border-radius: var(--radius-2xl); border: 1px solid var(--color-border-light); box-shadow: var(--shadow-md);" class="reveal">
          
          <form id="lead-form-element">
            <?= csrf_field() ?>
            <input type="hidden" id="calc_snapshot" name="calc_snapshot" value="">

            <!-- LANGKAH 1 (WAJIB) -->
            <div id="step-1-container">
              <h3 style="font-size: 18px; margin-bottom: 16px; border-bottom: 2px solid var(--color-orange); padding-bottom: 8px; display: inline-block;">Langkah 1 dari 2: Informasi Dasar</h3>
              
              <div class="form-group">
                <label for="form-name">Nama Lengkap *</label>
                <input type="text" id="form-name" name="name" class="form-control" required placeholder="Masukkan nama Anda">
              </div>

              <div class="form-group">
                <label for="form-phone">Nomor WhatsApp *</label>
                <input type="text" id="form-phone" name="phone" class="form-control" required placeholder="081234567890">
              </div>

              <div class="form-group">
                <label for="form-need">Kebutuhan Utama *</label>
                <select id="form-need" name="need" class="form-control" required>
                  <option value="Desain + Bangun">Desain + Bangun (RBK Design & Build)</option>
                  <option value="Desain rumah/bangunan (RBK Studio)">Desain rumah/bangunan (RBK Studio)</option>
                  <option value="Bangun rumah (RBK Konstruksi)">Bangun rumah (RBK Konstruksi)</option>
                  <option value="Renovasi (RenoVancy)">Renovasi (RenoVancy)</option>
                  <option value="Interior & taman (RBK Kreasi)">Interior & taman (RBK Kreasi)</option>
                  <option value="Kost / ruko / kantor / gudang">Kost / ruko / kantor / gudang</option>
                  <option value="Pembuatan RAB">Pembuatan RAB</option>
                </select>
              </div>

              <!-- Honeypot -->
              <input type="text" name="website_url_check" style="display:none !important;" tabindex="-1" autocomplete="off">

              <button type="button" id="btn-submit-step1" class="btn btn-orange" style="width: 100%; margin-top: 16px;">Lanjut ke Langkah 2 →</button>
            </div>

            <!-- LANGKAH 2 (OPSIONAL) -->
            <div id="step-2-container" style="display: none;">
              <h3 style="font-size: 18px; margin-bottom: 16px; border-bottom: 2px solid var(--color-orange); padding-bottom: 8px; display: inline-block;">Langkah 2 dari 2: Detail Proyek (Opsional)</h3>
              
              <div class="form-group">
                <label for="location">Lokasi Proyek (Kota / Kecamatan)</label>
                <input type="text" id="location" name="location" class="form-control" placeholder="Contoh: Pasirmulya, Bogor Barat">
              </div>

              <div class="form-group">
                <label for="land_size">Luas Tanah (m²)</label>
                <select id="land_size" name="land_size" class="form-control">
                  <option value="">-- Pilih Rencana Luas Tanah --</option>
                  <option value="< 100 m²">&lt; 100 m²</option>
                  <option value="100–200 m²">100–200 m²</option>
                  <option value="200–300 m²">200–300 m²</option>
                  <option value="300–500 m²">300–500 m²</option>
                  <option value="> 500 m²">&gt; 500 m²</option>
                </select>
              </div>

              <div class="form-group">
                <label for="building_size_m2">Luas Bangunan Rencana (m²)</label>
                <input type="number" id="building_size_m2" name="building_size_m2" class="form-control" placeholder="120">
              </div>

              <div class="form-group">
                <label for="budget_range">Budget Pembangunan Rencana</label>
                <select id="budget_range" name="budget_range" class="form-control">
                  <option value="Belum tahu">Belum tahu</option>
                  <option value="< Rp300 jt">&lt; Rp300 jt</option>
                  <option value="Rp300–700 jt">Rp300–700 jt</option>
                  <option value="Rp700 jt–1,5 M">Rp700 jt–1,5 M</option>
                  <option value="Rp1,5–3 M">Rp1,5–3 M</option>
                  <option value="> Rp3 M">&gt; Rp3 M</option>
                </select>
              </div>

              <div class="form-group" style="margin-top: 16px;">
                <label style="font-size: 13px; color: var(--color-muted-light);">
                  <input type="checkbox" name="consent" required checked> Saya setuju data saya dipakai RBK untuk menghubungi saya terkait konsultasi. <a href="/kebijakan-privasi" target="_blank" style="text-decoration: underline;">Kebijakan Privasi</a>.
                </label>
              </div>

              <div style="display: flex; gap: 12px; margin-top: 24px; flex-wrap: wrap;">
                <button type="submit" id="btn-submit-step2" class="btn btn-orange" style="flex: 1;">Kirim Informasi & Konsultasi</button>
                <button type="button" id="btn-skip-step2" class="btn btn-outline-dark">Lewati & Chat Sekarang</button>
              </div>
            </div>

          </form>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S19. FAQ ACCORDION -->
  <?php if (isset($sections['s19_faq'])): ?>
    <section id="faq" class="section alt">
      <div class="container container-narrow">
        <div style="text-align: center; margin-bottom: var(--space-8);" class="reveal">
          <span class="eyebrow"><?= e($sections['s19_faq']['eyebrow']) ?></span>
          <h2 style="font-size: var(--fs-h2);"><?= $sections['s19_faq']['title'] ?></h2>
        </div>

        <div class="faq-list reveal">
          <?php foreach ($faqs as $fq): ?>
            <details class="faq-accordion">
              <summary class="faq-summary">
                <span><?= e($fq['question']) ?></span>
                <span class="faq-icon">+</span>
              </summary>
              <div class="faq-content">
                <p><?= e($fq['answer']) ?></p>
              </div>
            </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- S21. FOOTER -->
  <footer style="background: var(--color-black); color: var(--color-white); padding: var(--space-12) 0 var(--space-8); border-top: 1px solid var(--color-border-dark);">
    <div class="container" style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: var(--space-8);">
      <div>
        <img src="/assets/img/logo-rbk-white.png" alt="Rancang Bangun Kreasi" style="height: 52px; width: auto; margin-bottom: 14px; display: block;">
        <p style="font-size: 13.5px; color: var(--color-muted-dark); max-width: 400px; margin-bottom: 16px;">
          <?= e($settings['company_pt_name'] ?? 'PT Rancang Bangun Sedaya') ?> — Jasa Arsitek (RBK Studio) dan Kontraktor Pembangunan (RBK Konstruksi) terpercaya di Bogor & Jabodetabek.
        </p>
        <p style="font-size: 13px; color: var(--color-muted-dark);">
          📍 <?= e($settings['office_address'] ?? 'Pasirmulya, Kota Bogor 16118') ?><br>
          🕒 <?= e($settings['office_hours'] ?? 'Senin–Sabtu, 08.00–17.00 WIB') ?>
        </p>
      </div>

      <div>
        <h4 style="font-size: 16px; color: var(--color-white); margin-bottom: 16px;">Tautan Cepat</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 14px; color: var(--color-muted-dark);">
          <li><a href="#layanan">Layanan Kami</a></li>
          <li><a href="#portofolio">Portofolio Proyek</a></li>
          <li><a href="#harga">Paket & Harga</a></li>
          <li><a href="#kalkulator">Kalkulator Estimasi</a></li>
          <li><a href="/kebijakan-privasi">Kebijakan Privasi</a></li>
          <li><a href="/admin/login" style="color: var(--color-orange); font-weight: 600;">🔐 Login Admin CMS</a></li>
        </ul>
      </div>

      <div>
        <h4 style="font-size: 16px; color: var(--color-white); margin-bottom: 16px;">Kontak & Medsos</h4>
        <p style="font-size: 14px; color: var(--color-muted-dark); line-height: 1.8;">
          📞 <?= e($settings['phone_number'] ?? '+62 812-3459-3742') ?><br>
          ✉️ <?= e($settings['contact_email'] ?? 'rancangbangunkreasi.official@gmail.com') ?>
        </p>
      </div>
    </div>

    <div class="container" style="margin-top: var(--space-8); padding-top: var(--space-6); border-top: 1px solid var(--color-border-dark); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 13px; color: var(--color-muted-dark);">
      <div>© <?= date('Y') ?> <?= e($settings['company_pt_name'] ?? 'PT Rancang Bangun Sedaya') ?>. All rights reserved.</div>
      <div><a href="/admin/login" style="color: var(--color-muted-dark); text-decoration: underline;">Dashboard CMS Admin</a></div>
    </div>
  </footer>


  <!-- FLOATING WHATSAPP BUTTON (DESKTOP) -->
  <a href="https://wa.me/<?= e($settings['whatsapp_number'] ?? '6281234593742') ?>" class="wa-float" aria-label="Chat WhatsApp Direct RBK" target="_blank">
    <img src="/assets/img/whatsapp-icon.png" alt="WhatsApp Direct RBK" width="60" height="60">
  </a>

  <!-- JS Scripts -->
  <script src="<?= asset('assets/js/main.js') ?>" defer></script>
  <script src="<?= asset('assets/js/calc.js') ?>" defer></script>
  <script src="<?= asset('assets/js/lead-form.js') ?>" defer></script>

</body>
</html>
