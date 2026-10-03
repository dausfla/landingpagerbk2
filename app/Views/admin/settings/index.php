<div style="margin-bottom: 24px;">
  <div class="chip-group">
    <a href="/admin/settings?tab=general" class="chip <?= $activeTab === 'general' ? 'active' : '' ?>">Umum & Brand</a>
    <a href="/admin/settings?tab=contact" class="chip <?= $activeTab === 'contact' ? 'active' : '' ?>">Kontak & Operasional</a>
    <a href="/admin/settings?tab=seo" class="chip <?= $activeTab === 'seo' ? 'active' : '' ?>">SEO & Meta</a>
    <a href="/admin/settings?tab=tracking" class="chip <?= $activeTab === 'tracking' ? 'active' : '' ?>">Tracking & Pixel</a>
    <a href="/admin/settings?tab=whatsapp" class="chip <?= $activeTab === 'whatsapp' ? 'active' : '' ?>">Template WhatsApp</a>
    <a href="/admin/settings?tab=announcement" class="chip <?= $activeTab === 'announcement' ? 'active' : '' ?>">Announcement Bar</a>
  </div>
</div>

<?php if (!empty($success)): ?>
  <div style="background: rgba(15, 14, 13, 0.05); border: 1px solid var(--color-black); color: var(--color-black); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 24px; font-weight: 600;">
    ✓ Pengaturan berhasil disimpan dan cache landing page telah diperbarui.
  </div>
<?php endif; ?>

<div style="max-width: 680px; background: var(--color-white); padding: 32px; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); box-shadow: var(--shadow-sm);">

  <form method="POST" action="/admin/settings/update">
    <?= csrf_field() ?>
    <input type="hidden" name="group" value="<?= e($activeTab) ?>">

    <?php if ($activeTab === 'general'): ?>
      <h3 style="font-family: var(--font-heading); font-size: 18px; margin-top: 0; margin-bottom: 20px;">Pengaturan Umum & Brand</h3>
      <div class="form-group">
        <label for="brand_name">Nama Brand Utama</label>
        <input type="text" id="brand_name" name="brand_name" class="form-control" value="<?= e($settings['brand_name'] ?? 'Rancang Bangun Kreasi (RBK)') ?>" required>
      </div>
      <div class="form-group">
        <label for="company_pt_name">Nama Resmi PT (Badan Hukum)</label>
        <input type="text" id="company_pt_name" name="company_pt_name" class="form-control" value="<?= e($settings['company_pt_name'] ?? 'PT Rancang Bangun Sedaya') ?>" required>
      </div>

    <?php elseif ($activeTab === 'contact'): ?>
      <h3 style="font-family: var(--font-heading); font-size: 18px; margin-top: 0; margin-bottom: 20px;">Kontak & Jam Operasional</h3>
      <div class="form-group">
        <label for="phone_number">Nomor Telepon Kontak</label>
        <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?= e($settings['phone_number'] ?? '+62 812-3459-3742') ?>" required>
      </div>
      <div class="form-group">
        <label for="whatsapp_number">Nomor WhatsApp Direct (Format 62...)</label>
        <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" value="<?= e($settings['whatsapp_number'] ?? '6281234593742') ?>" required>
      </div>
      <div class="form-group">
        <label for="contact_email">Alamat Email Official</label>
        <input type="email" id="contact_email" name="contact_email" class="form-control" value="<?= e($settings['contact_email'] ?? 'rancangbangunkreasi.official@gmail.com') ?>" required>
      </div>
      <div class="form-group">
        <label for="office_address">Alamat Kantor Resmi</label>
        <input type="text" id="office_address" name="office_address" class="form-control" value="<?= e($settings['office_address'] ?? 'Pasirmulya, Kota Bogor 16118') ?>" required>
      </div>
      <div class="form-group">
        <label for="office_hours">Jam Operasional Kantor</label>
        <input type="text" id="office_hours" name="office_hours" class="form-control" value="<?= e($settings['office_hours'] ?? 'Senin–Sabtu, 08.00–17.00 WIB') ?>" required>
      </div>

    <?php elseif ($activeTab === 'seo'): ?>
      <h3 style="font-family: var(--font-heading); font-size: 18px; margin-top: 0; margin-bottom: 20px;">Pengaturan SEO Default</h3>
      <div class="form-group">
        <label for="meta_title">Meta Title Landing Page</label>
        <input type="text" id="meta_title" name="meta_title" class="form-control" value="<?= e($settings['meta_title'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label for="meta_description">Meta Description</label>
        <textarea id="meta_description" name="meta_description" class="form-control" rows="4" required><?= e($settings['meta_description'] ?? '') ?></textarea>
      </div>

    <?php elseif ($activeTab === 'tracking'): ?>
      <h3 style="font-family: var(--font-heading); font-size: 18px; margin-top: 0; margin-bottom: 20px;">Konfigurasi Tracking Pixel & Ads ID</h3>
      <div class="form-group">
        <label for="gtm_id">Google Tag Manager (GTM) Container ID</label>
        <input type="text" id="gtm_id" name="gtm_id" class="form-control" placeholder="GTM-XXXXXXX" value="<?= e($settings['gtm_id'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="ga4_measurement_id">Google Analytics 4 (GA4) Measurement ID</label>
        <input type="text" id="ga4_measurement_id" name="ga4_measurement_id" class="form-control" placeholder="G-XXXXXXXXXX" value="<?= e($settings['ga4_measurement_id'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="meta_pixel_id">Meta (Facebook) Pixel ID</label>
        <input type="text" id="meta_pixel_id" name="meta_pixel_id" class="form-control" placeholder="1234567890" value="<?= e($settings['meta_pixel_id'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="tiktok_pixel_id">TikTok Pixel ID</label>
        <input type="text" id="tiktok_pixel_id" name="tiktok_pixel_id" class="form-control" placeholder="CXXXXXXXXX" value="<?= e($settings['tiktok_pixel_id'] ?? '') ?>">
      </div>

    <?php elseif ($activeTab === 'whatsapp'): ?>
      <h3 style="font-family: var(--font-heading); font-size: 18px; margin-top: 0; margin-bottom: 20px;">Template Pesan WhatsApp Automatic Direct</h3>
      <p style="font-size: 13px; color: var(--color-muted-light); margin-bottom: 16px;">
        Placeholder yang tersedia: <code>{nama}</code>, <code>{kode}</code>, <code>{kebutuhan}</code>, <code>{lokasi}</code>, <code>{luas_tanah}</code>, <code>{lantai}</code>, <code>{budget}</code>, <code>{paket}</code>.
      </p>
      <div class="form-group">
        <label for="wa_message_template">Format Template Pesan</label>
        <textarea id="wa_message_template" name="wa_message_template" class="form-control" rows="5" required><?= e($settings['wa_message_template'] ?? '') ?></textarea>
      </div>

    <?php elseif ($activeTab === 'announcement'): ?>
      <h3 style="font-family: var(--font-heading); font-size: 18px; margin-top: 0; margin-bottom: 20px;">Pengaturan Announcement Bar</h3>
      <div class="form-group">
        <label for="announcement_enabled">Status Tampilan</label>
        <select id="announcement_enabled" name="announcement_enabled" class="form-control">
          <option value="1" <?= ($settings['announcement_enabled'] ?? '1') === '1' ? 'selected' : '' ?>>Tampilkan Announcement Bar</option>
          <option value="0" <?= ($settings['announcement_enabled'] ?? '1') === '0' ? 'selected' : '' ?>>Sembunyikan</option>
        </select>
      </div>
      <div class="form-group">
        <label for="announcement_text">Teks Announcement</label>
        <input type="text" id="announcement_text" name="announcement_text" class="form-control" value="<?= e($settings['announcement_text'] ?? '') ?>">
      </div>
    <?php endif; ?>

    <div style="margin-top: 32px;">
      <button type="submit" class="btn btn-orange" style="padding: 12px 28px;">Simpan Perubahan Settings</button>
    </div>
  </form>

</div>
