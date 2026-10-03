<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;">
  <p style="color: var(--color-muted-light); font-size: 14px; margin: 0;">Kelola akun Super Admin yang memiliki akses penuh ke sistem dashboard.</p>
  <a href="/admin/users/create" class="btn btn-orange" style="padding: 10px 18px; font-size: 13.5px;">+ Tambah Admin Baru</a>
</div>

<div class="table-card">
  <table class="sg-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Peran</th>
        <th>Status</th>
        <th>Login Terakhir</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td>#<?= e($u['id']) ?></td>
          <td><strong><?= e($u['name']) ?></strong></td>
          <td><?= e($u['email']) ?></td>
          <td><span class="badge badge-black">Super Admin</span></td>
          <td>
            <?php if ($u['is_active']): ?>
              <span class="badge badge-orange">Aktif</span>
            <?php else: ?>
              <span class="badge badge-muted">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td><?= $u['last_login_at'] ? e(date('d/m/Y H:i', strtotime($u['last_login_at']))) : '-' ?></td>
          <td>
            <a href="/admin/users/edit/<?= $u['id'] ?>" class="btn btn-outline-dark" style="padding: 4px 10px; font-size: 12px;">Edit</a>
            <?php if ($u['id'] !== \App\Core\Auth::id()): ?>
              <button onclick="deleteUser(<?= $u['id'] ?>, '<?= e($u['name']) ?>')" class="btn btn-outline-dark" style="padding: 4px 10px; font-size: 12px; color: var(--color-orange-strong);">Hapus</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script>
  function deleteUser(id, name) {
    window.confirmAction(`Apakah Anda yakin ingin menghapus akun admin "${name}"?`, function() {
      fetch('/admin/users/delete/' + id, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message);
        }
      });
    });
  }
</script>
