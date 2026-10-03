<div style="max-width: 540px; background: var(--color-white); padding: 32px; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); box-shadow: var(--shadow-sm);">

  <?php if (!empty($error)): ?>
    <div style="background: rgba(221, 92, 62, 0.1); border: 1px solid var(--color-orange-strong); color: var(--color-orange-strong); padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13.5px; margin-bottom: 24px;">
      ! <?= e($error) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?= $user && isset($user['id']) ? '/admin/users/update/' . $user['id'] : '/admin/users/store' ?>">
    <?= csrf_field() ?>

    <div class="form-group">
      <label for="name">Nama Lengkap</label>
      <input type="text" id="name" name="name" class="form-control" value="<?= e($user['name'] ?? '') ?>" required>
      <?php if (!empty($errors['name'])): ?>
        <small style="color: var(--color-orange-strong); font-size: 12px;"><?= e($errors['name'][0]) ?></small>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="email">Alamat Email</label>
      <input type="email" id="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
      <?php if (!empty($errors['email'])): ?>
        <small style="color: var(--color-orange-strong); font-size: 12px;"><?= e($errors['email'][0]) ?></small>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="password">Password <?= $user && isset($user['id']) ? '(Biarkan kosong jika tidak diubah)' : '' ?></label>
      <input type="password" id="password" name="password" class="form-control" <?= $user && isset($user['id']) ? '' : 'required' ?> placeholder="Minimal 8 karakter">
      <?php if (!empty($errors['password'])): ?>
        <small style="color: var(--color-orange-strong); font-size: 12px;"><?= e($errors['password'][0]) ?></small>
      <?php endif; ?>
    </div>

    <?php if ($user && isset($user['id'])): ?>
      <div class="form-group">
        <label for="is_active">Status Akun</label>
        <select id="is_active" name="is_active" class="form-control">
          <option value="1" <?= ($user['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif</option>
          <option value="0" <?= ($user['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Nonaktif</option>
        </select>
      </div>
    <?php endif; ?>

    <div style="display: flex; gap: 12px; margin-top: 32px;">
      <button type="submit" class="btn btn-orange" style="padding: 12px 24px;">Simpan Data Admin</button>
      <a href="/admin/users" class="btn btn-outline-dark" style="padding: 12px 20px;">Batal</a>
    </div>
  </form>

</div>
