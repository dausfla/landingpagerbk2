<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
  <p style="color: var(--color-muted-light); font-size: 14px; margin: 0;">Kelola statistik & klaim bukti. Data bertanda <code>is_verified = 0</code> akan muncul di Go-Live Checklist Widget.</p>
</div>

<div class="table-card">
  <table class="sg-table">
    <thead>
      <tr>
        <th>Nilai</th>
        <th>Label Deskripsi</th>
        <th>Status Verifikasi (is_verified)</th>
        <th>Catatan Sumber</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($stats as $st): ?>
        <tr>
          <td><strong style="font-size: 18px; color: var(--color-orange);"><?= e($st['value']) ?></strong></td>
          <td><?= e($st['label']) ?></td>
          <td>
            <?php if ($st['is_verified']): ?>
              <span class="badge badge-black">Terverifikasi (1) ✓</span>
            <?php else: ?>
              <span class="badge badge-orange">0 [VERIFIKASI]</span>
            <?php endif; ?>
          </td>
          <td><small style="color: var(--color-muted-light);"><?= e($st['source_note']) ?></small></td>
          <td>
            <?php if ($st['is_published']): ?>
              <span class="badge badge-orange">Publikasi</span>
            <?php else: ?>
              <span class="badge badge-muted">Draft</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
