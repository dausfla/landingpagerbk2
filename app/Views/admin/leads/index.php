<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
  <form method="GET" action="/admin/leads" style="display: flex; gap: 12px; flex-wrap: wrap; flex: 1;">
    <input type="text" name="search" class="form-control" style="max-width: 240px;" placeholder="Cari Kode / Nama / WA..." value="<?= e($search) ?>">
    
    <select name="status" class="form-control" style="max-width: 180px;">
      <option value="">Semua Status</option>
      <option value="baru" <?= $status === 'baru' ? 'selected' : '' ?>>Baru</option>
      <option value="dihubungi" <?= $status === 'dihubungi' ? 'selected' : '' ?>>Dihubungi</option>
      <option value="survei_dijadwalkan" <?= $status === 'survei_dijadwalkan' ? 'selected' : '' ?>>Survei Dijadwalkan</option>
      <option value="survei_selesai" <?= $status === 'survei_selesai' ? 'selected' : '' ?>>Survei Selesai</option>
      <option value="penawaran_dikirim" <?= $status === 'penawaran_dikirim' ? 'selected' : '' ?>>Penawaran/RAB Dikirim</option>
      <option value="deal" <?= $status === 'deal' ? 'selected' : '' ?>>Deal ✓</option>
      <option value="batal" <?= $status === 'batal' ? 'selected' : '' ?>>Batal</option>
    </select>

    <button type="submit" class="btn btn-outline-dark" style="padding: 8px 16px;">Filter</button>
  </form>

  <a href="/admin/leads/export" class="btn btn-orange" style="padding: 10px 18px; font-size: 13.5px;">Export CSV</a>
</div>

<div class="table-card">
  <table class="sg-table">
    <thead>
      <tr>
        <th>Kode</th>
        <th>Tanggal</th>
        <th>Nama</th>
        <th>No. WhatsApp</th>
        <th>Kebutuhan</th>
        <th>Lokasi</th>
        <th>Status (Rules 8.2)</th>
        <th>PIC</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($leads as $l): ?>
        <tr <?= $l['is_duplicate'] ? 'style="background: #fff8f7;"' : '' ?>>
          <td>
            <strong><?= e($l['code']) ?></strong>
            <?php if ($l['is_duplicate']): ?>
              <span class="badge badge-orange" style="font-size: 9px; display: block; margin-top: 2px;">Duplikat</span>
            <?php endif; ?>
          </td>
          <td><?= e(date('d/m/Y H:i', strtotime($l['created_at']))) ?></td>
          <td><?= e($l['name']) ?></td>
          <td><a href="https://wa.me/<?= e($l['phone_normalized']) ?>" target="_blank" style="font-weight: 700; color: var(--color-black);"><?= e($l['phone']) ?></a></td>
          <td><?= e($l['need']) ?></td>
          <td><?= e($l['location'] ?? '-') ?></td>
          <td>
            <?php if ($l['status'] === 'baru'): ?>
              <span class="badge badge-orange">Baru</span>
            <?php elseif ($l['status'] === 'deal'): ?>
              <span class="badge badge-black">Deal ✓</span>
            <?php elseif ($l['status'] === 'batal'): ?>
              <span class="badge badge-muted">Batal</span>
            <?php else: ?>
              <span class="badge badge-outline"><?= e(ucwords(str_replace('_', ' ', $l['status']))) ?></span>
            <?php endif; ?>
          </td>
          <td><?= e($l['pic_name'] ?? '-') ?></td>
          <td>
            <a href="/admin/leads/detail/<?= $l['id'] ?>" class="btn btn-outline-dark" style="padding: 4px 10px; font-size: 12px;">Detail</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
