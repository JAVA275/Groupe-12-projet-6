<?php
// public/forgot-password.php
require_once __DIR__ . '/../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/models/User.php';

AuthMiddleware::start();
if (AuthMiddleware::isLoggedIn()) { header('Location: ' . BASE_PATH . '/dashboard.php'); exit; }

$message = '';
$messageType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $userModel = new User();
        $token = $userModel->createPasswordReset($email);
        // En production, envoyer un email avec le lien
        // Pour le développement, on affiche le lien
        if ($token) {
            $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . '/reset-password.php?token=' . $token;
            $message = "Un lien de réinitialisation a été généré. (Dev) Lien : <a href='$resetLink'>$resetLink</a>";
        } else {
            $message = "Si cet email existe dans notre base, un lien vous sera envoyé.";
        }
        $messageType = 'success';
    } else {
        $message = "Adresse email invalide.";
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mot de passe oublié — SujetsFS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_PATH ?>/css/main.css">
</head>
<body class="auth-page">
<div class="auth-card">
  <div class="auth-logo">
    <div class="brand-icon">📚</div>
    <div class="brand-text">SujetsFS</div>
  </div>
  <h1 class="auth-title">Mot de passe oublié</h1>
  <p class="auth-sub">Entrez votre email pour recevoir un lien de réinitialisation</p>

  <?php if ($message): ?>
  <div class="flash flash--<?= $messageType ?>" style="border-radius:7px;margin-bottom:1rem">
    <?= $message ?>
  </div>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_PATH ?>/forgot-password.php">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="form-group">
      <label class="form-label">Adresse email</label>
      <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
    </div>
    <button type="submit" class="btn btn--primary btn--block btn--lg">Envoyer le lien</button>
  </form>

  <div class="auth-footer">
    <a href="<?= BASE_PATH ?>/login.php">← Retour à la connexion</a>
  </div>
</div>
<script src="<?= BASE_PATH ?>/js/main.js"></script>
</body>
</html>
