<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terima Kasih — Permintaan Konsultasi Diterima | RBK</title>
  <link rel="stylesheet" href="<?= asset('assets/css/style.css') ?>">
</head>
<body style="background: var(--color-surface-light);">

  <div class="container container-narrow" style="padding: 60px 20px; text-align: center;">
    <div style="background: var(--color-white); padding: 48px 32px; border-radius: var(--radius-2xl); border: 1px solid var(--color-border-light); box-shadow: var(--shadow-md);">
      
      <span class="badge badge-orange" style="font-size: 14px; padding: 8px 16px; margin-bottom: 16px;">KONSULTASI BERHASIL DIKIRIM</span>
      
      <h1 style="font-size: 28px; margin-bottom: 12px; color: var(--color-black);">Terima Kasih, <?= e($lead['name'] ?? 'Bapak/Ibu') ?>!</h1>
      <p style="color: var(--color-muted-light); font-size: 16px; margin-bottom: 24px;">
        Permintaan konsultasi Anda telah tercatat dengan Kode Referensi: <strong style="color: var(--color-black);"><?= e($code) ?></strong>
      </p>

      <div style="background: var(--color-surface-light); padding: 20px; border-radius: var(--radius-lg); text-align: left; max-width: 400px; margin: 0 auto 32px; border: 1px solid var(--color-border-light);">
        <p style="font-size: 13.5px; margin-bottom: 6px;"><strong>Nama:</strong> <?= e($lead['name'] ?? '-') ?></p>
        <p style="font-size: 13.5px; margin-bottom: 6px;"><strong>No. WhatsApp:</strong> <?= e($lead['phone'] ?? '-') ?></p>
        <p style="font-size: 13.5px; margin-bottom: 6px;"><strong>Kebutuhan:</strong> <?= e($lead['need'] ?? '-') ?></p>
        <?php if (!empty($lead['location'])): ?>
          <p style="font-size: 13.5px; margin-bottom: 0;"><strong>Lokasi Proyek:</strong> <?= e($lead['location']) ?></p>
        <?php endif; ?>
      </div>

      <p style="font-size: 14.5px; color: var(--color-muted-light); margin-bottom: 24px;">
        Anda akan terhubung ke WhatsApp resmi RBK secara otomatis dalam 3 detik...
      </p>

      <a id="wa-redirect-link" href="<?= e($waUrl) ?>" class="btn btn-orange" style="padding: 16px 32px; font-size: 16px;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle; margin-right: 8px;"><path d="M12.031 2c-5.514 0-9.999 4.486-9.999 10 0 1.763.458 3.486 1.332 5.006l-1.364 4.994 5.118-1.342c1.464.798 3.12 1.219 4.913 1.219 5.514 0 9.999-4.486 9.999-10 0-5.514-4.485-10-9.999-10z"/></svg>
        Lanjut Chat di WhatsApp Sekarang
      </a>

      <div style="margin-top: 24px;">
        <a href="/" style="font-size: 13.5px; color: var(--color-muted-light); text-decoration: underline;">Kembali ke Beranda Utama</a>
      </div>
    </div>
  </div>

  <script src="<?= asset('assets/js/main.js') ?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Fire Conversion Tracking Events (Section 10.3)
      if (window.trackEvent) {
        window.trackEvent('generate_lead', { event_id: '<?= e($code) ?>' });
      }

      // Auto redirect to WhatsApp after 3 seconds
      setTimeout(() => {
        const link = document.getElementById('wa-redirect-link');
        if (link) {
          window.location.href = link.href;
        }
      }, 3000);
    });
  </script>

</body>
</html>
