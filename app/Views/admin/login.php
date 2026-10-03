<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Super Admin | RBK Studio × RBK Konstruksi</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/styleguide.css') ?>">

  <style>
    body {
      background: var(--color-black);
      color: var(--color-white);
      font-family: var(--font-body);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      margin: 0;
    }

    .login-box {
      background: var(--color-surface-dark);
      border: 1px solid var(--color-border-dark);
      border-radius: var(--radius-xl);
      padding: 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: var(--shadow-lg);
    }

    .login-box .brand-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .login-box .brand-header h1 {
      font-family: var(--font-heading);
      font-size: 22px;
      margin: 0;
      color: var(--color-white);
    }

    .login-box .brand-header p {
      color: var(--color-orange);
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      margin-top: 6px;
    }

    .alert-error {
      background: rgba(221, 92, 62, 0.15);
      border: 1px solid var(--color-orange-strong);
      color: var(--color-orange-soft);
      padding: 12px 16px;
      border-radius: var(--radius-sm);
      font-size: 13.5px;
      margin-bottom: 24px;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <div class="brand-header">
      <img src="/assets/img/logo-rbk.png" alt="Rancang Bangun Kreasi Logo" style="height: 60px; width: auto; margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto;">
      <p style="font-weight: 700; color: var(--color-orange); letter-spacing: 0.05em;">SUPER ADMIN ACCESS ONLY</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert-error">
        ! <?= e($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="/admin/login">
      <?= csrf_field() ?>

      <div class="form-group">
        <label for="email" style="color: var(--color-white);">Alamat Email</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= e($email ?? '') ?>" required autofocus placeholder="admin@rancangbangunkreasi.id">
      </div>

      <div class="form-group" style="margin-bottom: 28px;">
        <label for="password" style="color: var(--color-white);">Password</label>
        <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
      </div>

      <button type="submit" class="btn btn-orange" style="width: 100%; padding: 14px;">Masuk ke Dashboard</button>
    </form>
  </div>

</body>
</html>
