<?php
// public/reset-password.php
require_once __DIR__ . '/../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/models/User.php';

AuthMiddleware::start();

$token = $_GET['token'] ?? '';
$userModel = new User();
$resetRecord = $userModel->verifyResetToken($token);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$resetRecord) {
        $error = 'Token invalide ou expiré.';
    } else {
        $newPwd = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        if (strlen($newPwd) < 8) {
            $error = 'Le mot de passe doit contenir au moins 8 caractères.';
        } elseif ($newPwd !== $confirm) {
            $error = 'Les mots de passe ne correspondent pas.';
        } else {
            $userModel->consumeResetToken($token, $newPwd);
            redirect('/login.php', 'Mot de passe réinitialisé avec succès !');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réinitialisation — SujetsFS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_PATH ?>/css/main.css">
</head>
<body class="auth-page">
<div class="auth-card">
  <div class="auth-logo">
    <div class="brand-icon">🔒</div>
    <div class="brand-text">SujetsFS</div>
  </div>
  <h1 class="auth-title">Nouveau mot de passe</h1>

  <?php if (!$resetRecord && !$_POST): ?>
  <div class="flash flash--error" style="border-radius:7px;margin-bottom:1rem">
    Ce lien est invalide ou a expiré. <a href="<?= BASE_PATH ?>/forgot-password.php">Faire une nouvelle demande</a>.
  </div>
  <?php else: ?>

  <?php if ($error): ?>
  <div class="flash flash--error" style="border-radius:7px;margin-bottom:1rem"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_PATH ?>/reset-password.php?token=<?= e($token) ?>">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-group">
      <label class="form-label">Nouveau mot de passe</label>
      <div style="position:relative">
        <input type="password" id="rst_pwd" name="password" class="form-control" minlength="8" required placeholder="Minimum 8 caractères">
        <button type="button" class="toggle-password" data-target="rst_pwd"
                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem;line-height:1">>👁️<#128065;</button>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Confirmer le mot de passe</label>
      <div style="position:relative">
        <input type="password" id="rst_confirm" name="confirm" class="form-control" required placeholder="••••••••">
        <button type="button" class="toggle-password" data-target="rst_confirm"
                style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem;line-height:1">>👁️<#128065;</button>
      </div>
    </div>
    <button type="submit" class="btn btn--primary btn--block btn--lg">Réinitialiser</button>
  </form>
  <?php endif; ?>

  <div class="auth-footer"><a href="<?= BASE_PATH ?>/login.php">← Connexion</a></div>
</div>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>
