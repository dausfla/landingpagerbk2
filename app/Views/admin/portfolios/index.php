<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
  <div>
    <h2 style="font-size: 20px; font-weight: 800; margin-bottom: 4px;">Portofolio Proyek RBK</h2>
    <p style="color: var(--color-muted-light); font-size: 14px; margin: 0;">Kelola karya perancangan & konstruksi RBK Studio & RBK Konstruksi.</p>
  </div>
  <a href="/admin/portfolios/create" class="btn btn-orange" style="padding: 10px 18px; font-size: 14px;">+ Tambah Proyek Baru</a>
</div>

<div class="table-card">
  <table class="sg-table">
    <thead>
      <tr>
        <th>Foto (Before / After)</th>
        <th>Judul Proyek</th>
        <th>Kategori</th>
        <th>Layanan</th>
        <th>Lokasi / Tahun</th>
        <th>Urutan</th>
        <th>Featured</th>
        <th>Status</th>
        <th style="text-align: right;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($portfolios as $p): ?>
        <?php 
          $beforeThumb = !empty($p['before_image']) ? $p['before_image'] : '/assets/img/' . e($p['slug']) . '-before.webp';
          $afterThumb = !empty($p['after_image']) ? $p['after_image'] : '/assets/img/' . e($p['slug']) . '.webp';
        ?>
        <tr>
          <td>
            <div style="display: flex; gap: 4px;">
              <div title="Foto Sebelum" style="width: 44px; height: 32px; border-radius: 4px; overflow: hidden; border: 1px solid #cbd5e1; background: #e2e8f0; position: relative;">
                <img src="<?= e($beforeThumb) ?>" alt="Before" style="width: 100%; height: 100%; object-fit: cover;">
                <span style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(220, 38, 38, 0.85); color: #fff; font-size: 8px; text-align: center; line-height: 10px;">SEBELUM</span>
              </div>
              <div title="Foto Sesudah" style="width: 44px; height: 32px; border-radius: 4px; overflow: hidden; border: 1px solid #cbd5e1; background: #e2e8f0; position: relative;">
                <img src="<?= e($afterThumb) ?>" alt="After" style="width: 100%; height: 100%; object-fit: cover;">
                <span style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(22, 163, 74, 0.85); color: #fff; font-size: 8px; text-align: center; line-height: 10px;">SESUDAH</span>
              </div>
            </div>
          </td>
          <td><strong><?= e($p['title']) ?></strong></td>
          <td><span class="badge badge-black"><?= e($p['category_name']) ?></span></td>
          <td><?= e(ucwords(str_replace('_', ' ', $p['service_type']))) ?></td>
          <td><?= e($p['location']) ?> (<?= e($p['year']) ?>)</td>
          <td><?= e($p['sort_order']) ?></td>
          <td><?= $p['is_featured'] ? '⭐ Ya' : 'Tidak' ?></td>
          <td>
            <?php if ($p['is_published']): ?>
              <span class="badge badge-orange">Publikasi</span>
            <?php else: ?>
              <span class="badge badge-muted">Draft</span>
            <?php endif; ?>
          </td>
          <td style="text-align: right;">
            <div style="display: inline-flex; gap: 8px;">
              <a href="/admin/portfolios/edit/<?= $p['id'] ?>" class="btn btn-outline-dark" style="padding: 6px 12px; font-size: 12px;">Edit</a>
              <form action="/admin/portfolios/delete/<?= $p['id'] ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus portofolio ini?');" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <button type="submit" class="btn" style="padding: 6px 12px; font-size: 12px; background: #dc2626; color: #fff; border: none; border-radius: 6px; cursor: pointer;">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
