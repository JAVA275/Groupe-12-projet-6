<?php
// public/message.php
require_once __DIR__ . '/../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/models/Message.php';
require_once __DIR__ . '/../src/models/User.php';

AuthMiddleware::requireLogin();
$user     = AuthMiddleware::currentUser();
$msgModel = new Message();

$id  = (int)($_GET['id'] ?? 0);
$msg = $msgModel->findById($id);

if (!$msg || ($msg['destinataire_id'] !== $user['id'] && $msg['expediteur_id'] !== $user['id'])) {
    redirect('/messages.php', 'Message introuvable.', 'error');
}

// Marquer comme lu si destinataire
if ($msg['destinataire_id'] === $user['id'] && !$msg['lu']) {
    $msgModel->marquerLu($id, $user['id']);
}

$userModel = new User();
$userFull  = $userModel->findById($user['id']);
$pageTitle = 'Message : ' . $msg['sujet'];
require_once __DIR__ . '/../views/shared/header.php';
?>

<main class="container" style="padding:2rem 1rem;max-width:700px;margin:0 auto">
  <a href="<?= BASE_PATH ?>/messages.php" style="color:#6c63ff;text-decoration:none;font-size:.9rem">← Retour à la messagerie</a>

  <div style="background:#fff;border-radius:1rem;padding:2rem;box-shadow:0 2px 12px rgba(0,0,0,.08);margin-top:1.25rem">

    <h1 style="font-size:1.4rem;margin:0 0 1rem"><?= e($msg['sujet']) ?></h1>

    <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding-bottom:1rem;border-bottom:1px solid #eee;margin-bottom:1.25rem">
      <div>
        <div style="font-size:.85rem;color:#888">De</div>
        <strong><?= e($msg['exp_prenom'] . ' ' . $msg['exp_nom']) ?></strong>
        <span style="font-size:.8rem;background:#eee;border-radius:999px;padding:1px 8px;margin-left:.4rem"><?= ucfirst(e($msg['exp_role'])) ?></span>
        <?php if ($msg['exp_tel']): ?>
        <div style="margin-top:.4rem;display:flex;gap:.5rem;flex-wrap:wrap">
          <a href="tel:<?= e($msg['exp_tel']) ?>" class="btn btn--sm" style="background:#27ae60;color:#fff;font-size:.8rem">
            📞 <?= e($msg['exp_tel']) ?>
          </a>
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$msg['exp_tel']) ?>" target="_blank"
             class="btn btn--sm" style="background:#25d366;color:#fff;font-size:.8rem">
            WhatsApp
          </a>
        </div>
        <?php endif; ?>
      </div>
      <div style="text-align:right">
        <div style="font-size:.85rem;color:#888">À</div>
        <strong><?= e($msg['dest_prenom'] . ' ' . $msg['dest_nom']) ?></strong>
        <div style="font-size:.8rem;color:#aaa;margin-top:.25rem"><?= formatDateHeure($msg['date_envoi']) ?></div>
      </div>
    </div>

    <div style="line-height:1.7;white-space:pre-wrap;color:#333"><?= e($msg['contenu']) ?></div>

    <!-- Répondre si on est le destinataire -->
    <?php if ($msg['destinataire_id'] === $user['id']): ?>
    <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #eee">
      <h3 style="font-size:1rem;margin:0 0 1rem">↩️ Répondre</h3>
      <form action="<?= BASE_PATH ?>/actions/send-message.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="destinataire_id" value="<?= $msg['expediteur_id'] ?>">
        <input type="hidden" name="sujet" value="Re: <?= e($msg['sujet']) ?>">
        <div class="form-group">
          <textarea name="contenu" class="form-control" rows="4"
                    placeholder="Votre réponse..." required style="resize:vertical"></textarea>
        </div>
        <button type="submit" class="btn btn--primary">Envoyer la réponse ✉️</button>
      </form>
    </div>
    <?php endif; ?>

  </div>
</main>

<?php require_once __DIR__ . '/../views/shared/footer.php'; ?>
