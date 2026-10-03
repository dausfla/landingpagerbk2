<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 28px;">
  
  <!-- LEFT COLUMN: DETAIL & TIMELINE -->
  <div>
    <!-- LEAD DETAIL CARD -->
    <div style="background: var(--color-white); padding: 28px; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); margin-bottom: 28px; box-shadow: var(--shadow-sm);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--color-border-light); padding-bottom: 16px;">
        <div>
          <span class="badge badge-black"><?= e($lead['code']) ?></span>
          <h2 style="font-size: 22px; margin-top: 6px;"><?= e($lead['name']) ?></h2>
        </div>
        <a href="https://wa.me/<?= e($lead['phone_normalized']) ?>" target="_blank" class="btn btn-orange" style="padding: 10px 18px; font-size: 13px;">Chat WA Direct</a>
      </div>

      <table class="sg-table" style="font-size: 13.5px;">
        <tr><td style="font-weight: 700; width: 30%;">Nomor WhatsApp</td><td><?= e($lead['phone']) ?> (Normalized: <?= e($lead['phone_normalized']) ?>)</td></tr>
        <tr><td style="font-weight: 700;">Kebutuhan Utama</td><td><?= e($lead['need']) ?></td></tr>
        <tr><td style="font-weight: 700;">Lokasi Proyek</td><td><?= e($lead['location'] ?? '-') ?></td></tr>
        <tr><td style="font-weight: 700;">Luas Tanah / Bangunan</td><td><?= e($lead['land_size'] ?? '-') ?> / <?= $lead['building_size_m2'] ? e($lead['building_size_m2']) . ' m²' : '-' ?></td></tr>
        <tr><td style="font-weight: 700;">Budget Pembangunan</td><td><?= e($lead['budget_range'] ?? '-') ?></td></tr>
        <tr><td style="font-weight: 700;">Paket Pilihan</td><td><?= e($lead['package_choice'] ?? '-') ?></td></tr>
        <tr><td style="font-weight: 700;">Catatan Tambahan</td><td><?= e($lead['notes'] ?? '-') ?></td></tr>
      </table>

      <!-- UTM Attribution Data -->
      <h3 style="font-size: 16px; margin: 24px 0 12px;">Atribusi Tracking & Perangkat</h3>
      <table class="sg-table" style="font-size: 12.5px;">
        <tr><td style="font-weight: 700; width: 30%;">UTM Source / Medium / Campaign</td><td><?= e($lead['utm_source'] ?? '-') ?> / <?= e($lead['utm_medium'] ?? '-') ?> / <?= e($lead['utm_campaign'] ?? '-') ?></td></tr>
        <tr><td style="font-weight: 700;">GCLID / FBCLID</td><td><?= e($lead['gclid'] ?? '-') ?> / <?= e($lead['fbclid'] ?? '-') ?></td></tr>
        <tr><td style="font-weight: 700;">Perangkat & Referrer</td><td><?= e($lead['device'] ?? '-') ?> · <?= e($lead['referrer'] ?? '-') ?></td></tr>
      </table>
    </div>

    <!-- ACTIVITY TIMELINE -->
    <div style="background: var(--color-white); padding: 28px; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); box-shadow: var(--shadow-sm);">
      <h3 style="font-size: 18px; margin-top: 0; margin-bottom: 20px;">Timeline Aktivitas Pipeline</h3>
      <?php if (empty($activities)): ?>
        <p style="color: var(--color-muted-light); font-size: 13.5px;">Belum ada catatan aktivitas.</p>
      <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 16px;">
          <?php foreach ($activities as $act): ?>
            <div style="border-left: 3px solid var(--color-orange); padding-left: 14px;">
              <div style="font-size: 12px; color: var(--color-muted-light);"><?= date('d/m/Y H:i', strtotime($act['created_at'])) ?> oleh <?= e($act['user_name'] ?? 'Sistem') ?></div>
              <strong style="font-size: 14px;"><?= e(ucwords(str_replace('_', ' ', $act['from_status'] ?? '')) ?: 'Baru') ?> → <?= e(ucwords(str_replace('_', ' ', $act['to_status'] ?? ''))) ?></strong>
              <?php if (!empty($act['note'])): ?>
                <p style="font-size: 13px; color: var(--color-muted-light); margin-top: 4px;"><?= e($act['note']) ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- RIGHT COLUMN: UPDATE PIPELINE STATUS -->
  <div>
    <div style="background: var(--color-white); padding: 24px; border-radius: var(--radius-xl); border: 1px solid var(--color-border-light); box-shadow: var(--shadow-sm); position: sticky; top: 90px;">
      <h3 style="font-size: 18px; margin-top: 0; margin-bottom: 20px;">Update Pipeline Lead</h3>

      <form method="POST" action="/admin/leads/update-status/<?= $lead['id'] ?>">
        <?= csrf_field() ?>

        <div class="form-group">
          <label for="status">Status Pipeline (Rules 8.2)</label>
          <select id="status" name="status" class="form-control" onchange="toggleLostReason(this.value)">
            <option value="baru" <?= $lead['status'] === 'baru' ? 'selected' : '' ?>>Baru (Oren Penuh)</option>
            <option value="dihubungi" <?= $lead['status'] === 'dihubungi' ? 'selected' : '' ?>>Dihubungi (Outline Hitam)</option>
            <option value="survei_dijadwalkan" <?= $lead['status'] === 'survei_dijadwalkan' ? 'selected' : '' ?>>Survei Dijadwalkan</option>
            <option value="survei_selesai" <?= $lead['status'] === 'survei_selesai' ? 'selected' : '' ?>>Survei Selesai</option>
            <option value="penawaran_dikirim" <?= $lead['status'] === 'penawaran_dikirim' ? 'selected' : '' ?>>Penawaran/RAB Dikirim</option>
            <option value="deal" <?= $lead['status'] === 'deal' ? 'selected' : '' ?>>Deal ✓ (Hitam Penuh)</option>
            <option value="batal" <?= $lead['status'] === 'batal' ? 'selected' : '' ?>>Batal (Coret Abu)</option>
          </select>
        </div>

        <div id="lost-reason-group" class="form-group" style="display: <?= $lead['status'] === 'batal' ? 'block' : 'none' ?>;">
          <label for="lost_reason">Alasan Batal (Wajib diisi jika Batal)</label>
          <select id="lost_reason" name="lost_reason" class="form-control">
            <option value="Harga">Harga</option>
            <option value="Waktu">Waktu</option>
            <option value="Lokasi di luar area">Lokasi di luar area</option>
            <option value="Tidak merespons">Tidak merespons</option>
            <option value="Pilih kompetitor">Pilih kompetitor</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>

        <div class="form-group">
          <label for="assigned_to">Assign PIC (Staf Admin)</label>
          <select id="assigned_to" name="assigned_to" class="form-control">
            <option value="">-- Belum Ditugaskan --</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= $u['id'] ?>" <?= $lead['assigned_to'] == $u['id'] ? 'selected' : '' ?>><?= e($u['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="follow_up_at">Tanggal Follow Up</label>
          <input type="datetime-local" id="follow_up_at" name="follow_up_at" class="form-control" value="<?= $lead['follow_up_at'] ? date('Y-m-d\TH:i', strtotime($lead['follow_up_at'])) : '' ?>">
        </div>

        <div class="form-group">
          <label for="estimated_value">Estimasi Nilai Proyek (Rp)</label>
          <input type="number" id="estimated_value" name="estimated_value" class="form-control" value="<?= e($lead['estimated_value'] ?? '') ?>" placeholder="500000000">
        </div>

        <div class="form-group">
          <label for="note">Catatan Tambahan Activity</label>
          <textarea id="note" name="note" class="form-control" rows="3" placeholder="Catatan hasil diskusi..."></textarea>
        </div>

        <button type="submit" class="btn btn-orange" style="width: 100%; margin-top: 16px;">Simpan Perubahan Pipeline</button>
      </form>
    </div>
  </div>

</div>

<script>
  function toggleLostReason(val) {
    const group = document.getElementById('lost-reason-group');
    group.style.display = (val === 'batal') ? 'block' : 'none';
  }
</script>
