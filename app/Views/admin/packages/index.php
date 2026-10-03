<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
  <p style="color: var(--color-muted-light); font-size: 14px; margin: 0;">Satu-satunya sumber angka harga untuk kartu harga, kalkulator, announcement bar, chip hero, dan FAQ.</p>
  <a href="/admin/packages/create" class="btn btn-orange" style="padding: 10px 18px; font-size: 13.5px;">+ Tambah Paket Baru</a>
</div>

<div class="table-card">
  <table class="sg-table">
    <thead>
      <tr>
        <th>Layanan</th>
        <th>Nama Paket</th>
        <th>Tagline</th>
        <th>Rentang Harga (/m²)</th>
        <th>Badge</th>
        <th>Status</th>
        <th>Urutan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($packages as $pkg): ?>
        <tr>
          <td><span class="badge badge-black"><?= e(ucfirst($pkg['service_type'])) ?></span></td>
          <td><strong><?= e($pkg['name']) ?></strong></td>
          <td><?= e($pkg['tagline']) ?></td>
          <td><strong><?= format_rupiah_compact($pkg['price_min'], $pkg['price_max']) ?></strong></td>
          <td>
            <?php if (!empty($pkg['badge'])): ?>
              <span class="badge badge-orange"><?= e($pkg['badge']) ?></span>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td>
            <?php if ($pkg['is_published']): ?>
              <span class="badge badge-orange">Publikasi</span>
            <?php else: ?>
              <span class="badge badge-muted">Draft</span>
            <?php endif; ?>
          </td>
          <td><?= $pkg['sort_order'] ?></td>
          <td>
            <a href="/admin/packages/edit/<?= $pkg['id'] ?>" class="btn btn-outline-dark" style="padding: 4px 10px; font-size: 12px;">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
