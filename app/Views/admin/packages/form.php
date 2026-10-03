<div style="max-width: 680px; background: var(--color-white); padding: 32px; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); box-shadow: var(--shadow-sm);">

  <?php if (!empty($error)): ?>
    <div style="background: rgba(221, 92, 62, 0.1); border: 1px solid var(--color-orange-strong); color: var(--color-orange-strong); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 24px;">
      ! <?= e($error) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?= $package && isset($package['id']) ? '/admin/packages/update/' . $package['id'] : '/admin/packages/store' ?>">
    <?= csrf_field() ?>

    <div class="form-group">
      <label for="service_type">Jenis Layanan Paket</label>
      <select id="service_type" name="service_type" class="form-control" required>
        <option value="desain" <?= ($package['service_type'] ?? '') === 'desain' ? 'selected' : '' ?>>Desain (RBK Studio)</option>
        <option value="bangun" <?= ($package['service_type'] ?? '') === 'bangun' ? 'selected' : '' ?>>Bangun (RBK Konstruksi)</option>
      </select>
    </div>

    <div class="form-group">
      <label for="name">Nama Paket</label>
      <input type="text" id="name" name="name" class="form-control" value="<?= e($package['name'] ?? '') ?>" required placeholder="Basic / Standard / Premium">
    </div>

    <div class="form-group">
      <label for="tagline">Tagline Singkat</label>
      <input type="text" id="tagline" name="tagline" class="form-control" value="<?= e($package['tagline'] ?? '') ?>" placeholder="Best Balance Between Design x Investment">
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label for="price_min">Harga Minimum (Rp / m²)</label>
        <input type="number" id="price_min" name="price_min" class="form-control" value="<?= e($package['price_min'] ?? '') ?>" required placeholder="80000">
      </div>

      <div class="form-group">
        <label for="price_max">Harga Maksimal (Opsional jika rentang)</label>
        <input type="number" id="price_max" name="price_max" class="form-control" value="<?= e($package['price_max'] ?? '') ?>" placeholder="Kosongkan jika harga tunggal">
      </div>
    </div>

    <div class="form-group">
      <label for="badge">Badge Highlight (Opsional)</label>
      <input type="text" id="badge" name="badge" class="form-control" value="<?= e($package['badge'] ?? '') ?>" placeholder="Recommended / Signature Service / Paling seimbang">
    </div>

    <div class="form-group">
      <label for="suitable_for">Peruntukan (Pisahkan dengan koma)</label>
      <input type="text" id="suitable_for" name="suitable_for" class="form-control" value="<?= is_array($package['suitable_for'] ?? null) ? e(implode(', ', $package['suitable_for'])) : e($package['suitable_for'] ?? '') ?>" placeholder="Rumah Premium, Ruko, Bangunan Kost">
    </div>

    <div class="form-group">
      <label for="cta_label">Teks Label CTA Button</label>
      <input type="text" id="cta_label" name="cta_label" class="form-control" value="<?= e($package['cta_label'] ?? 'Pilih Paket') ?>" required>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
      <div class="form-group">
        <label for="sort_order">Urutan Tampilan</label>
        <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= e($package['sort_order'] ?? 1) ?>" required>
      </div>

      <div class="form-group">
        <label for="is_published">Status Publikasi</label>
        <select id="is_published" name="is_published" class="form-control">
          <option value="1" <?= ($package['is_published'] ?? 1) == 1 ? 'selected' : '' ?>>Publikasikan</option>
          <option value="0" <?= ($package['is_published'] ?? 1) == 0 ? 'selected' : '' ?>>Draft / Sembunyikan</option>
        </select>
      </div>
    </div>

    <div style="display: flex; gap: 12px; margin-top: 32px;">
      <button type="submit" class="btn btn-orange" style="padding: 12px 24px;">Simpan Paket & Update Cache</button>
      <a href="/admin/packages" class="btn btn-outline-dark" style="padding: 12px 20px;">Batal</a>
    </div>
  </form>

</div>
