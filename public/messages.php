<?php
// public/messages.php
require_once __DIR__ . '/../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/models/Message.php';
require_once __DIR__ . '/../src/models/User.php';

AuthMiddleware::requireLogin();
$user    = AuthMiddleware::currentUser();
$msgModel = new Message();
$userModel = new User();

$onglet  = $_GET['tab'] ?? 'recus';
$recus   = $msgModel->getRecus($user['id']);
$envoyes = $msgModel->getEnvoyes($user['id']);
$contacts = $msgModel->getAdminsEnseignants();
// Retirer soi-même de la liste
$contacts = array_filter($contacts, fn($c) => $c['id'] !== $user['id']);

$pageTitle = 'Messagerie';
$userFull  = $userModel->findById($user['id']);
require_once __DIR__ . '/../views/shared/header.php';
?>

<main class="container" style="padding:2rem 1rem;max-width:900px;margin:0 auto">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div>
      <h1 style="margin:0;font-size:1.6rem">💬 Messagerie</h1>
      <p style="margin:.25rem 0 0;color:#888">Communiquez avec les enseignants et l'administration</p>
    </div>
    <button onclick="document.getElementById('modal-nouveau').style.display='flex'"
            class="btn btn--primary">✉️ Nouveau message</button>
  </div>

  <!-- Onglets -->
  <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;border-bottom:2px solid #eee;padding-bottom:.5rem">
    <a href="<?= BASE_PATH ?>/messages.php?tab=recus"
       style="padding:.5rem 1rem;border-radius:.5rem .5rem 0 0;text-decoration:none;font-weight:600;
              <?= $onglet === 'recus' ? 'background:var(--primary,#6c63ff);color:#fff' : 'color:#555' ?>">
      📥 Reçus
      <?php $nonLus = array_filter($recus, fn($m) => !$m['lu']); ?>
      <?php if (count($nonLus) > 0): ?>
        <span style="background:#e74c3c;color:#fff;border-radius:999px;padding:1px 7px;font-size:.75rem;margin-left:.3rem"><?= count($nonLus) ?></span>
      <?php endif; ?>
    </a>
    <a href="<?= BASE_PATH ?>/messages.php?tab=envoyes"
       style="padding:.5rem 1rem;border-radius:.5rem .5rem 0 0;text-decoration:none;font-weight:600;
              <?= $onglet === 'envoyes' ? 'background:var(--primary,#6c63ff);color:#fff' : 'color:#555' ?>">
      📤 Envoyés
    </a>
  </div>

  <!-- Liste messages reçus -->
  <?php if ($onglet === 'recus'): ?>
    <?php if (empty($recus)): ?>
      <div style="text-align:center;padding:3rem;color:#aaa">
        <div style="font-size:3rem">📭</div>
        <p>Aucun message reçu</p>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:.75rem">
        <?php foreach ($recus as $msg): ?>
        <div style="background:#fff;border-radius:.75rem;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.08);
                    border-left:4px solid <?= $msg['lu'] ? '#ddd' : '#6c63ff' ?>;
                    <?= !$msg['lu'] ? 'font-weight:600' : '' ?>">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem">
            <div>
              <span style="font-size:.8rem;background:<?= $msg['exp_role']==='admin'?'#e74c3c':($msg['exp_role']==='enseignant'?'#3498db':'#27ae60') ?>;
                    color:#fff;border-radius:999px;padding:2px 8px;margin-right:.5rem">
                <?= ucfirst(e($msg['exp_role'])) ?>
              </span>
              <strong><?= e($msg['exp_prenom'] . ' ' . $msg['exp_nom']) ?></strong>
              <?php if (!$msg['lu']): ?><span style="color:#6c63ff;font-size:.8rem"> • Non lu</span><?php endif; ?>
            </div>
            <span style="font-size:.8rem;color:#aaa"><?= formatDateHeure($msg['date_envoi']) ?></span>
          </div>
          <div style="margin-top:.4rem;font-size:1rem"><?= e($msg['sujet']) ?></div>
          <div style="display:flex;gap:.5rem;margin-top:.6rem;flex-wrap:wrap">
            <a href="<?= BASE_PATH ?>/message.php?id=<?= $msg['id'] ?>" class="btn btn--outline btn--sm">Lire</a>
            <?php if ($msg['exp_tel']): ?>
            <a href="tel:<?= e($msg['exp_tel']) ?>" class="btn btn--sm" style="background:#27ae60;color:#fff">📞 Appeler</a>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$msg['exp_tel']) ?>" target="_blank"
               class="btn btn--sm" style="background:#25d366;color:#fff">WhatsApp</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  <!-- Liste messages envoyés -->
  <?php else: ?>
    <?php if (empty($envoyes)): ?>
      <div style="text-align:center;padding:3rem;color:#aaa">
        <div style="font-size:3rem">📬</div>
        <p>Aucun message envoyé</p>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;gap:.75rem">
        <?php foreach ($envoyes as $msg): ?>
        <div style="background:#fff;border-radius:.75rem;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #ddd">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem">
            <div>
              <span style="font-size:.8rem;color:#888">À :</span>
              <strong> <?= e($msg['dest_prenom'] . ' ' . $msg['dest_nom']) ?></strong>
              <span style="font-size:.8rem;background:#eee;border-radius:999px;padding:1px 7px;margin-left:.3rem"><?= ucfirst(e($msg['dest_role'])) ?></span>
              <?= $msg['lu'] ? '<span style="font-size:.8rem;color:#27ae60"> ✓ Lu</span>' : '<span style="font-size:.8rem;color:#aaa"> • En attente</span>' ?>
            </div>
            <span style="font-size:.8rem;color:#aaa"><?= formatDateHeure($msg['date_envoi']) ?></span>
          </div>
          <div style="margin-top:.4rem"><?= e($msg['sujet']) ?></div>
          <a href="<?= BASE_PATH ?>/message.php?id=<?= $msg['id'] ?>" class="btn btn--outline btn--sm" style="margin-top:.5rem">Voir</a>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</main>

<!-- Modal nouveau message -->
<div id="modal-nouveau" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:1rem">
  <div style="background:#fff;border-radius:1rem;padding:2rem;width:100%;max-width:520px;box-shadow:0 8px 32px rgba(0,0,0,.2)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
      <h2 style="margin:0;font-size:1.2rem">✉️ Nouveau message</h2>
      <button onclick="document.getElementById('modal-nouveau').style.display='none'"
              style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:#888">✕</button>
    </div>
    <form action="<?= BASE_PATH ?>/actions/send-message.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="form-group">
        <label class="form-label">Destinataire</label>
        <select name="destinataire_id" class="form-control" required>
          <option value=""> Choisir un destinataire </option>
          <?php foreach ($contacts as $contact): ?>
          <option value="<?= $contact['id'] ?>">
            <?= e($contact['prenom'] . ' ' . $contact['nom']) ?> — <?= ucfirst(e($contact['role'])) ?>
            <?= $contact['telephone'] ? ' (' . e($contact['telephone']) . ')' : '' ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Sujet</label>
        <input type="text" name="sujet" class="form-control" placeholder="Ex: Question sur l'examen de POO" required>
      </div>
      <div class="form-group">
        <label class="form-label">Message</label>
        <textarea name="contenu" class="form-control" rows="5" placeholder="Écrivez votre message..." required style="resize:vertical"></textarea>
      </div>
      <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:1rem">
        <button type="button" onclick="document.getElementById('modal-nouveau').style.display='none'"
                class="btn btn--outline">Annuler</button>
        <button type="submit" class="btn btn--primary">Envoyer ✉️</button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../views/shared/footer.php'; ?>
