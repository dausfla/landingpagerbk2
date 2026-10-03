<style>
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
  }

  .kpi-card {
    background: var(--color-white);
    border: 1px solid var(--color-border-light);
    border-radius: var(--radius-xl);
    padding: 20px;
    box-shadow: var(--shadow-sm);
  }

  .kpi-card .label {
    font-size: 13px;
    color: var(--color-muted-light);
    font-weight: 600;
  }

  .kpi-card .value {
    font-family: var(--font-heading);
    font-size: 28px;
    font-weight: 800;
    color: var(--color-black);
    margin-top: 8px;
  }

  .kpi-card .sub {
    font-size: 12px;
    color: var(--color-muted-light);
    margin-top: 4px;
  }

  .checklist-widget {
    background: var(--color-white);
    border: 1px solid var(--color-border-light);
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 32px;
  }

  .checklist-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--color-border-light);
    font-size: 14px;
  }

  .checklist-item:last-child {
    border-bottom: none;
  }

  .table-card {
    background: var(--color-white);
    border: 1px solid var(--color-border-light);
    border-radius: var(--radius-xl);
    padding: 24px;
  }
</style>

<!-- KPI CARDS -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="label">Lead Hari Ini</div>
    <div class="value"><?= number_format($todayLeads) ?></div>
    <div class="sub">7 hari: <?= number_format($sevenDaysLeads) ?> · 30 hari: <?= number_format($thirtyDaysLeads) ?></div>
  </div>

  <div class="kpi-card">
    <div class="label">Kelengkapan Lead</div>
    <div class="value" style="color: var(--color-orange);"><?= $completePercent ?>%</div>
    <div class="sub">Persentase Form Step 2 Lengkap</div>
  </div>

  <div class="kpi-card">
    <div class="label">Klik WhatsApp</div>
    <div class="value"><?= number_format($waClicksToday) ?></div>
    <div class="sub">Interaksi tombol WA melayang</div>
  </div>

  <div class="kpi-card">
    <div class="label">Median Waktu Respons</div>
    <div class="value"><?= $medianResponseMin > 0 ? "{$medianResponseMin} mnt" : "N/A" ?></div>
    <div class="sub">Dihitung pada jam kerja</div>
  </div>
</div>

<!-- GO-LIVE CHECKLIST WIDGET -->
<div class="checklist-widget">
  <h3 style="font-family: var(--font-heading); font-size: 18px; margin-top: 0; margin-bottom: 16px;">Widget Checklist Go-Live Website</h3>
  
  <div class="checklist-item">
    <span>Data klaim belum terverifikasi (<code>is_verified = 0</code>)</span>
    <?php if ($checklist['unverified_claims'] > 0): ?>
      <span class="badge badge-orange"><?= $checklist['unverified_claims'] ?> Perlu Verifikasi</span>
    <?php else: ?>
      <span class="badge badge-black">Terverifikasi ✓</span>
    <?php endif; ?>
  </div>

  <div class="checklist-item">
    <span>Konfigurasi Tracking ID (GTM / GA4 / Meta Pixel)</span>
    <?php if ($checklist['missing_tracking']): ?>
      <span class="badge badge-orange">Belum Diisi</span>
    <?php else: ?>
      <span class="badge badge-black">Terkonfigurasi ✓</span>
    <?php endif; ?>
  </div>

  <div class="checklist-item">
    <span>Jumlah Testimoni Klien Dipublikasi</span>
    <?php if ($checklist['testimonials_count'] == 0): ?>
      <span class="badge badge-outline">0 (Section Otomatis Sembunyi)</span>
    <?php else: ?>
      <span class="badge badge-black"><?= $checklist['testimonials_count'] ?> Dipublikasi ✓</span>
    <?php endif; ?>
  </div>

  <div class="checklist-item">
    <span>Pengubahan Password Default Super Admin</span>
    <?php if ($checklist['must_change_password']): ?>
      <span class="badge badge-orange">Wajib Ganti Password</span>
    <?php else: ?>
      <span class="badge badge-black">Aman ✓</span>
    <?php endif; ?>
  </div>

  <div class="checklist-item">
    <span>Pengujian Email Notifikasi SMTP</span>
    <?php if (!$checklist['smtp_tested']): ?>
      <span class="badge badge-outline">Belum Diuji</span>
    <?php else: ?>
      <span class="badge badge-black">Aktif ✓</span>
    <?php endif; ?>
  </div>
</div>

<!-- RECENT LEADS TABLE -->
<div class="table-card">
  <h3 style="font-family: var(--font-heading); font-size: 18px; margin-top: 0; margin-bottom: 16px;">10 Lead Terbaru Masuk</h3>
  
  <?php if (empty($recentLeads)): ?>
    <p style="color: var(--color-muted-light); font-size: 14px;">Belum ada lead yang masuk.</p>
  <?php else: ?>
    <table class="sg-table">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Tanggal</th>
          <th>Nama</th>
          <th>No. WhatsApp</th>
          <th>Kebutuhan</th>
          <th>Status (Rules 8.2)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recentLeads as $lead): ?>
          <tr>
            <td><strong><?= e($lead['code']) ?></strong></td>
            <td><?= e(date('d/m/Y H:i', strtotime($lead['created_at']))) ?></td>
            <td><?= e($lead['name']) ?></td>
            <td><a href="https://wa.me/<?= e($lead['phone_normalized']) ?>" target="_blank" style="color: var(--color-black); font-weight: 700;"><?= e($lead['phone']) ?></a></td>
            <td><?= e($lead['need']) ?></td>
            <td>
              <?php if ($lead['status'] === 'baru'): ?>
                <span class="badge badge-orange">Baru</span>
              <?php elseif ($lead['status'] === 'deal'): ?>
                <span class="badge badge-black">Deal ✓</span>
              <?php elseif ($lead['status'] === 'batal'): ?>
                <span class="badge badge-muted">Batal</span>
              <?php else: ?>
                <span class="badge badge-outline"><?= e(ucwords(str_replace('_', ' ', $lead['status']))) ?></span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
