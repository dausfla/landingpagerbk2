<div style="max-width: 840px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h2 style="font-size: 22px; font-weight: 800; margin: 0;"><?= e($title) ?></h2>
    <a href="/admin/portfolios" class="btn btn-outline-dark" style="padding: 8px 16px; font-size: 13.5px;">← Kembali ke Portofolio</a>
  </div>

  <?php if (!empty($error)): ?>
    <div style="background: #fee2e2; border: 1px solid #f87171; color: #991b1b; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px;">
      <?= e($error) ?>
    </div>
  <?php endif; ?>

  <div style="background: #fff; padding: 28px; border-radius: 12px; border: 1px solid var(--color-border-light); box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
    <form action="<?= $portfolio ? '/admin/portfolios/update/' . $portfolio['id'] : '/admin/portfolios/store' ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="form-group">
        <label>Judul Proyek *</label>
        <input type="text" name="title" class="form-control" value="<?= e($portfolio['title'] ?? '') ?>" required placeholder="Contoh: RenoVancy Rumah Mr. Putra">
      </div>

      <div class="form-group">
        <label>Slug URL (Opsional, otomatis dari judul jika kosong)</label>
        <input type="text" name="slug" class="form-control" value="<?= e($portfolio['slug'] ?? '') ?>" placeholder="renovancy-rumah-mr-putra">
      </div>

      <!-- UPLOAD BEFORE & AFTER PHOTOS FOR SLIDER -->
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin: 20px 0;">
        <h4 style="font-size: 15px; font-weight: 800; color: var(--color-black); margin-bottom: 6px;">🖼️ Foto Slider Sebelum & Sesudah (Before & After)</h4>
        <p style="font-size: 13px; color: var(--color-muted-light); margin-bottom: 16px;">Upload foto perbandingan sebelum dan sesudah renovasi/pembangunan untuk slider interaktif di landing page (Format: JPG, PNG, WEBP).</p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <!-- Foto Sebelum (Before) -->
          <div class="form-group" style="margin: 0;">
            <label style="font-weight: 700; color: #dc2626;">Foto Sebelum (Before)</label>
            <input type="file" name="before_image" accept="image/*" class="form-control" style="padding: 10px; height: auto;">
            
            <?php 
              $beforeSrc = !empty($portfolio['before_image']) 
                ? $portfolio['before_image'] 
                : (!empty($portfolio['slug']) && file_exists(__DIR__ . '/../../../../public/assets/img/' . $portfolio['slug'] . '-before.webp') ? '/assets/img/' . $portfolio['slug'] . '-before.webp' : '/assets/img/rumah-a-before.webp');
            ?>
            <div style="margin-top: 10px; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; aspect-ratio: 16/10; background: #e2e8f0; position: relative;">
              <img src="<?= e($beforeSrc) ?>" alt="Preview Sebelum" style="width: 100%; height: 100%; object-fit: cover;">
              <span style="position: absolute; bottom: 6px; left: 6px; background: rgba(0,0,0,0.7); color: #fff; font-size: 11px; padding: 2px 8px; border-radius: 4px;">Foto Sebelum</span>
            </div>
          </div>

          <!-- Foto Sesudah (After) -->
          <div class="form-group" style="margin: 0;">
            <label style="font-weight: 700; color: #16a34a;">Foto Sesudah (After)</label>
            <input type="file" name="after_image" accept="image/*" class="form-control" style="padding: 10px; height: auto;">
            
            <?php 
              $afterSrc = !empty($portfolio['after_image']) 
                ? $portfolio['after_image'] 
                : (!empty($portfolio['slug']) && file_exists(__DIR__ . '/../../../../public/assets/img/' . $portfolio['slug'] . '.webp') ? '/assets/img/' . $portfolio['slug'] . '.webp' : '/assets/img/rumah-a-after.webp');
            ?>
            <div style="margin-top: 10px; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; aspect-ratio: 16/10; background: #e2e8f0; position: relative;">
              <img src="<?= e($afterSrc) ?>" alt="Preview Sesudah" style="width: 100%; height: 100%; object-fit: cover;">
              <span style="position: absolute; bottom: 6px; left: 6px; background: rgba(0,0,0,0.7); color: #fff; font-size: 11px; padding: 2px 8px; border-radius: 4px;">Foto Sesudah</span>
            </div>
          </div>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="form-group">
          <label>Kategori Proyek *</label>
          <select name="category_id" class="form-control" required>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= isset($portfolio['category_id']) && $portfolio['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                <?= e($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Tipe Layanan *</label>
          <select name="service_type" class="form-control" required>
            <option value="studio" <?= isset($portfolio['service_type']) && $portfolio['service_type'] == 'studio' ? 'selected' : '' ?>>Desain Arsitektur (RBK Studio)</option>
            <option value="design_build" <?= isset($portfolio['service_type']) && $portfolio['service_type'] == 'design_build' ? 'selected' : '' ?>>Design & Build (RBK Konstruksi)</option>
            <option value="renovasi" <?= isset($portfolio['service_type']) && $portfolio['service_type'] == 'renovasi' ? 'selected' : '' ?>>Renovasi Rumah</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div class="form-group">
          <label>Lokasi Proyek</label>
          <input type="text" name="location" class="form-control" value="<?= e($portfolio['location'] ?? 'Bogor') ?>" placeholder="Bogor / Dramaga / Jakarta">
        </div>

        <div class="form-group">
          <label>Tahun Pengerjaan</label>
          <input type="text" name="year" class="form-control" value="<?= e($portfolio['year'] ?? date('Y')) ?>" placeholder="2023">
        </div>
      </div>

      <div class="form-group">
        <label>Ringkasan Deskripsi Proyek</label>
        <textarea name="short_desc" class="form-control" rows="3" placeholder="Transformasi total rumah tinggal menjadi hunian tropis modern..."><?= e($portfolio['short_desc'] ?? '') ?></textarea>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
        <div class="form-group">
          <label>Urutan Tampil (Sort Order)</label>
          <input type="number" name="sort_order" class="form-control" value="<?= e($portfolio['sort_order'] ?? 0) ?>">
        </div>

        <div class="form-group">
          <label>Tampilkan di Featured (Hero/Spotlight)?</label>
          <select name="is_featured" class="form-control">
            <option value="0" <?= isset($portfolio['is_featured']) && !$portfolio['is_featured'] ? 'selected' : '' ?>>Tidak</option>
            <option value="1" <?= isset($portfolio['is_featured']) && $portfolio['is_featured'] ? 'selected' : '' ?>>Ya</option>
          </select>
        </div>

        <div class="form-group">
          <label>Status Publikasi</label>
          <select name="is_published" class="form-control">
            <option value="1" <?= isset($portfolio['is_published']) && $portfolio['is_published'] ? 'selected' : '' ?>>Publikasikan</option>
            <option value="0" <?= isset($portfolio['is_published']) && !$portfolio['is_published'] ? 'selected' : '' ?>>Simpan Draft</option>
          </select>
        </div>
      </div>

      <div style="margin-top: 24px; text-align: right;">
        <button type="submit" class="btn btn-orange" style="padding: 12px 28px; font-size: 15px;">Simpan Portofolio Proyek</button>
      </div>
    </form>
  </div>
</div>
