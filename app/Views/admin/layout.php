<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Dashboard Admin') ?> | RBK Studio × RBK Konstruksi</title>
  
  <!-- Preconnect Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/styleguide.css') ?>">
  
  <style>
    /* Admin Layout Specific CSS */
    body {
      background: var(--color-surface-light);
      color: var(--color-black);
      font-family: var(--font-body);
      margin: 0;
      padding: 0;
      display: flex;
      min-height: 100vh;
    }

    .admin-sidebar {
      width: 260px;
      background: var(--color-black);
      color: var(--color-white);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      border-right: 1px solid var(--color-border-dark);
    }

    .admin-sidebar .brand {
      padding: 24px 20px;
      border-bottom: 1px solid var(--color-border-dark);
    }

    .admin-sidebar .brand h2 {
      font-family: var(--font-heading);
      font-size: 16px;
      margin: 0;
      color: var(--color-white);
      font-weight: 800;
    }

    .admin-sidebar .brand span {
      font-size: 11.5px;
      color: var(--color-orange);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      display: block;
      margin-top: 4px;
    }

    .admin-nav {
      padding: 16px 10px;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 4px;
      overflow-y: auto;
    }

    .admin-nav a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 14px;
      color: var(--color-muted-dark);
      text-decoration: none;
      font-weight: 600;
      font-size: 14px;
      border-radius: var(--radius-sm);
      transition: background var(--duration-fast), color var(--duration-fast);
    }

    .admin-nav a:hover, .admin-nav a.active {
      background: var(--color-surface-dark);
      color: var(--color-white);
    }

    .admin-nav a.active {
      border-left: 3px solid var(--color-orange);
      color: var(--color-white);
    }

    .admin-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .admin-topbar {
      height: 64px;
      background: var(--color-white);
      border-bottom: 1px solid var(--color-border-light);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 28px;
    }

    .admin-topbar h1 {
      font-family: var(--font-heading);
      font-size: 18px;
      font-weight: 800;
      margin: 0;
    }

    .admin-topbar .user-info {
      display: flex;
      align-items: center;
      gap: 16px;
      font-size: 13.5px;
    }

    .admin-content {
      padding: 28px;
      flex: 1;
    }

    /* Admin Form Controls Styling */
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
      margin-bottom: 20px;
      text-align: left;
      width: 100%;
    }

    .form-group label, label {
      display: block;
      font-weight: 700;
      font-size: 14px;
      color: var(--color-black);
      margin-bottom: 4px;
    }

    .form-control,
    input[type="text"].form-control,
    input[type="email"].form-control,
    input[type="number"].form-control,
    input[type="password"].form-control,
    select.form-control,
    textarea.form-control {
      width: 100%;
      height: 46px;
      padding: 10px 16px;
      font-size: 14.5px;
      color: var(--color-black);
      background-color: var(--color-white);
      border: 1.5px solid var(--color-border-light);
      border-radius: var(--radius-lg);
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      box-sizing: border-box;
    }

    .form-control:focus {
      border-color: var(--color-orange) !important;
      box-shadow: 0 0 0 3px rgba(221, 92, 62, 0.2) !important;
      outline: none;
    }

    select.form-control {
      appearance: none;
      -webkit-appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23dd5c3e' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 14px center;
      background-size: 16px 16px;
      padding-right: 40px !important;
      cursor: pointer;
    }

    /* Modal Component */
    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15, 14, 13, 0.6);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .modal-backdrop.active {
      display: flex;
    }

    .modal-box {
      background: var(--color-white);
      border-radius: var(--radius-xl);
      padding: 24px;
      max-width: 440px;
      width: 90%;
      box-shadow: var(--shadow-lg);
    }

    @media (max-width: 760px) {
      body {
        flex-direction: column;
      }
      .admin-sidebar {
        width: 100%;
      }
    }
  </style>
</head>
<body>

  <!-- SIDEBAR NAVIGATION -->
  <aside class="admin-sidebar">
    <div class="brand">
      <img src="/assets/img/logo-rbk-white.png" alt="RBK Studio Logo" style="height: 44px; width: auto; display: block;">
      <span style="font-size: 11px; color: var(--color-orange); letter-spacing: 0.05em; font-weight: 700; margin-top: 4px; display: block;">DASHBOARD ADMIN</span>
    </div>
    
    <nav class="admin-nav">
      <a href="/admin" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/admin' ? 'active' : '' ?>">
        <span>📊</span> Beranda Dashboard
      </a>
      <a href="/admin/leads" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/leads') ? 'active' : '' ?>">
        <span>📥</span> Data Leads
      </a>
      <a href="/admin/packages" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/packages') ? 'active' : '' ?>">
        <span>🏷️</span> Paket & Harga
      </a>
      <a href="/admin/portfolios" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/portfolios') ? 'active' : '' ?>">
        <span>🖼️</span> Portofolio
      </a>
      <a href="/admin/settings" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/settings') ? 'active' : '' ?>">
        <span>⚙️</span> Pengaturan Sistem
      </a>
      <a href="/admin/users" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/users') ? 'active' : '' ?>">
        <span>👥</span> Pengguna (Admin)
      </a>
      <a href="/styleguide" target="_blank" style="margin-top: auto; color: var(--color-orange);">
        <span>🎨</span> Open Styleguide
      </a>
    </nav>
  </aside>

  <!-- MAIN CONTAINER -->
  <div class="admin-main">
    <header class="admin-topbar">
      <h1><?= e($title ?? 'Dashboard Admin') ?></h1>
      <div class="user-info">
        <span>Halo, <strong><?= e(\App\Core\Auth::user()['name'] ?? 'Super Admin') ?></strong></span>
        <a href="/admin/logout" class="btn btn-outline-dark" style="padding: 6px 14px; font-size: 12px;">Logout</a>
      </div>
    </header>

    <main class="admin-content">
      <?= $content ?>
    </main>
  </div>

  <!-- CUSTOM CONFIRMATION MODAL -->
  <div id="custom-modal" class="modal-backdrop">
    <div class="modal-box">
      <h3 id="modal-title" style="font-family: var(--font-heading); font-size: 18px; margin-bottom: 12px;">Konfirmasi Aksi</h3>
      <p id="modal-body" style="font-size: 14px; color: var(--color-muted-light); margin-bottom: 24px;">Apakah Anda yakin ingin melanjutkan aksi ini?</p>
      <div style="display: flex; justify-content: flex-end; gap: 12px;">
        <button id="modal-cancel-btn" class="btn btn-outline-dark" style="padding: 8px 16px; font-size: 13px;">Batal</button>
        <button id="modal-confirm-btn" class="btn btn-orange" style="padding: 8px 16px; font-size: 13px;">Yakin, Lanjutkan</button>
      </div>
    </div>
  </div>

  <script>
    // Global Confirmation Modal Helper (No browser alert/confirm)
    window.confirmAction = function(message, onConfirm) {
      const modal = document.getElementById('custom-modal');
      document.getElementById('modal-body').innerText = message;
      modal.classList.add('active');

      const cancelBtn = document.getElementById('modal-cancel-btn');
      const confirmBtn = document.getElementById('modal-confirm-btn');

      const closeHandler = () => {
        modal.classList.remove('active');
        cancelBtn.removeEventListener('click', closeHandler);
        confirmBtn.removeEventListener('click', confirmHandler);
      };

      const confirmHandler = () => {
        closeHandler();
        onConfirm();
      };

      cancelBtn.addEventListener('click', closeHandler);
      confirmBtn.addEventListener('click', confirmHandler);
    };
  </script>

</body>
</html>
