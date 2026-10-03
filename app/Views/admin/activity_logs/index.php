<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
  <p style="color: var(--color-muted-light); font-size: 14px; margin: 0;">Jejak rekam aktivitas pengolahan data oleh Super Admin (Audit Trail).</p>
</div>

<div class="table-card">
  <table class="sg-table">
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Pengguna</th>
        <th>Aksi</th>
        <th>Entitas</th>
        <th>Entity ID</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($logs as $log): ?>
        <tr>
          <td><?= e(date('d/m/Y H:i:s', strtotime($log['created_at']))) ?></td>
          <td><strong><?= e($log['user_name'] ?? 'Sistem') ?></strong></td>
          <td><span class="badge badge-black"><?= e($log['action']) ?></span></td>
          <td><code><?= e($log['entity']) ?></code></td>
          <td>#<?= e($log['entity_id'] ?? '-') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
