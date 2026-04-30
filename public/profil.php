<?php
// public/profil.php
require_once __DIR__ . '/../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Message.php';
require_once __DIR__ . '/../src/config/database.php';

AuthMiddleware::requireLogin();
$user      = AuthMiddleware::currentUser();
$userModel = new User();
$msgModel  = new Message();
$userFull  = $userModel->findById($user['id']);
$db        = Database::getInstance();

$nbSujets          = (int)$db->query("SELECT COUNT(*) FROM sujets WHERE auteur_id = {$user['id']}")->fetchColumn();
$nbTelechargements = (int)$db->query("SELECT COUNT(*) FROM telechargements WHERE utilisateur_id = {$user['id']}")->fetchColumn();
$nbCommentaires    = (int)$db->query("SELECT COUNT(*) FROM commentaires WHERE utilisateur_id = {$user['id']}")->fetchColumn();
$nbMessagesNonLus  = $msgModel->countNonLus($user['id']);

$passwordError = '';
$infoError     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // Mise à jour infos + téléphone
    if ($_POST['action'] === 'update_info') {
        if (!csrf_verify()) { $infoError = 'Token invalide.'; }
        else {
            $nom       = trim($_POST['nom'] ?? '');
            $prenom    = trim($_POST['prenom'] ?? '');
            $telephone = trim($_POST['telephone'] ?? '');
            if (empty($nom) || empty($prenom)) {
                $infoError = 'Nom et prénom sont obligatoires.';
            } else {
                $userModel->update($user['id'], [
                    'nom'       => $nom,
                    'prenom'    => $prenom,
                    'telephone' => $telephone ?: null,
                ]);
                redirect('/profil.php', 'Informations mises à jour !');
            }
        }
    }

    // Upload avatar
    if ($_POST['action'] === 'update_avatar') {
        if (!empty($_FILES['avatar']['name'])) {
            $allowedTypes = ['image/jpeg','image/png','image/gif','image/webp'];
            if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
                redirect('/profil.php', 'Format non autorisé. JPG, PNG, GIF ou WEBP uniquement.', 'error');
            }
            if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                redirect('/profil.php', 'Image trop lourde. Maximum 2MB.', 'error');
            }
            $ext        = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $nomFichier = 'avatar_' . $user['id'] . '_' . time() . '.' . strtolower($ext);
            $dest       = __DIR__ . '/uploads/avatars/' . $nomFichier;
            if (!is_dir(__DIR__ . '/uploads/avatars/')) mkdir(__DIR__ . '/uploads/avatars/', 0755, true);
            if (!empty($userFull['avatar'])) {
                $old = __DIR__ . '/uploads/avatars/' . $userFull['avatar'];
                if (file_exists($old)) unlink($old);
            }
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                $userModel->update($user['id'], ['avatar' => $nomFichier]);
                redirect('/profil.php', 'Photo de profil mise à jour !');
            } else {
                redirect('/profil.php', 'Erreur lors de l\'upload.', 'error');
            }
        }
    }

    // Changement mot de passe
    if ($_POST['action'] === 'change_password') {
        if (!csrf_verify()) { $passwordError = 'Token de sécurité invalide.'; }
        else {
            $stmt = $db->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = ?");
            $stmt->execute([$user['id']]);
            $record     = $stmt->fetch();
            $currentPwd = $_POST['current_password'] ?? '';
            $newPwd     = $_POST['new_password'] ?? '';
            $confirm    = $_POST['confirm_password'] ?? '';

            if (!password_verify($currentPwd, $record['mot_de_passe'] ?? '')) {
                $passwordError = 'Mot de passe actuel incorrect.';
            } elseif (strlen($newPwd) < 8) {
                $passwordError = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
            } elseif ($newPwd !== $confirm) {
                $passwordError = 'Les mots de passe ne correspondent pas.';
            } else {
                $userModel->updatePassword($user['id'], $newPwd);
                redirect('/profil.php', 'Mot de passe modifié avec succès !');
            }
        }
    }
}

$pageTitle = 'Mon profil';
require_once __DIR__ . '/../views/shared/header.php';
?>

<main class="container" style="padding:2rem 1rem;max-width:900px;margin:0 auto">

  <!-- En-tête profil -->
  <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;margin-bottom:2rem">
    <div style="position:relative">
      <?php if (!empty($userFull['avatar'])): ?>
        <img src="<?= BASE_PATH ?>/uploads/avatars/<?= e($userFull['avatar']) ?>" alt="Avatar"
             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #6c63ff">
      <?php else: ?>
        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#6c63ff,#a78bfa);
                    display:flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;font-weight:700;border:3px solid #6c63ff">
          <?= strtoupper(substr($userFull['prenom'],0,1)) ?>
        </div>
      <?php endif; ?>
    </div>
    <div>
      <h1 style="margin:0;font-size:1.6rem"><?= e($userFull['prenom'] . ' ' . $userFull['nom']) ?></h1>
      <span style="background:<?= $user['role']==='admin'?'#e74c3c':($user['role']==='enseignant'?'#3498db':'#27ae60') ?>;
                   color:#fff;border-radius:999px;padding:3px 12px;font-size:.85rem">
        <?= ucfirst(e($user['role'])) ?>
      </span>
      <?php if (!empty($userFull['telephone'])): ?>
      <div style="margin-top:.4rem;display:flex;gap:.5rem;flex-wrap:wrap">
        <a href="tel:<?= e($userFull['telephone']) ?>" style="font-size:.85rem;color:#27ae60;text-decoration:none">
          📞 <?= e($userFull['telephone']) ?>
        </a>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$userFull['telephone']) ?>" target="_blank"
           style="font-size:.85rem;color:#25d366;text-decoration:none">💬 WhatsApp</a>
      </div>
      <?php endif; ?>
    </div>
    <?php if ($nbMessagesNonLus > 0): ?>
    <a href="<?= BASE_PATH ?>/messages.php" class="btn btn--primary" style="margin-left:auto">
      💬 <?= $nbMessagesNonLus ?> message<?= $nbMessagesNonLus>1?'s':'' ?> non lu<?= $nbMessagesNonLus>1?'s':'' ?>
    </a>
    <?php endif; ?>
  </div>

  <!-- Stats -->
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:2rem">
    <?php foreach ([
        ['📄','Sujets publiés',$nbSujets],
        ['📥','Téléchargements',$nbTelechargements],
        ['💬','Commentaires',$nbCommentaires],
        ['✉️','Messages non lus',$nbMessagesNonLus],
    ] as [$icon,$label,$val]): ?>
    <div style="background:#fff;border-radius:.75rem;padding:1.25rem;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.07)">
      <div style="font-size:1.6rem"><?= $icon ?></div>
      <div style="font-size:1.4rem;font-weight:700"><?= $val ?></div>
      <div style="font-size:.8rem;color:#888"><?= $label ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;flex-wrap:wrap">

    <!-- Infos personnelles + téléphone -->
    <div style="background:#fff;border-radius:.75rem;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.07)">
      <h2 style="font-size:1.1rem;margin:0 0 1.25rem">👤 Informations personnelles</h2>
      <?php if ($infoError): ?>
        <div style="background:#fef2f2;color:#e74c3c;padding:.75rem;border-radius:.5rem;margin-bottom:1rem"><?= e($infoError) ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="action" value="update_info">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="form-group">
          <label class="form-label">Nom</label>
          <input type="text" name="nom" class="form-control" value="<?= e($userFull['nom']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Prénom</label>
          <input type="text" name="prenom" class="form-control" value="<?= e($userFull['prenom']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" value="<?= e($userFull['email']) ?>" disabled style="opacity:.6">
        </div>
        <div class="form-group">
          <label class="form-label">📞 Numéro de téléphone</label>
          <input type="tel" name="telephone" class="form-control"
                 value="<?= e($userFull['telephone'] ?? '') ?>"
                 placeholder="+237 6XX XXX XXX">
          <small style="color:#888;font-size:.8em">Visible par les autres membres pour vous contacter</small>
        </div>
        <button type="submit" class="btn btn--primary btn--block">Enregistrer</button>
      </form>
    </div>

    <!-- Changer mot de passe -->
    <div style="background:#fff;border-radius:.75rem;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.07)">
      <h2 style="font-size:1.1rem;margin:0 0 1.25rem">🔒 Changer le mot de passe</h2>
      <?php if ($passwordError): ?>
        <div style="background:#fef2f2;color:#e74c3c;padding:.75rem;border-radius:.5rem;margin-bottom:1rem"><?= e($passwordError) ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="action" value="change_password">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="form-group">
          <label class="form-label">Mot de passe actuel</label>
          <div style="position:relative">
            <input type="password" id="cur_pwd" name="current_password" class="form-control" required>
            <button type="button" class="toggle-password" data-target="cur_pwd"
                    style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem;line-height:1">👁️</button>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nouveau mot de passe</label>
          <div style="position:relative">
            <input type="password" id="new_pwd" name="new_password" class="form-control" minlength="8" required>
            <button type="button" class="toggle-password" data-target="new_pwd"
                    style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem;line-height:1">👁️</button>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Confirmer</label>
          <div style="position:relative">
            <input type="password" id="conf_pwd" name="confirm_password" class="form-control" required>
            <button type="button" class="toggle-password" data-target="conf_pwd"
                    style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem;line-height:1">👁️</button>
          </div>
        </div>
        <button type="submit" class="btn btn--primary btn--block">Modifier le mot de passe</button>
      </form>
    </div>

    <!-- Avatar -->
    <div style="background:#fff;border-radius:.75rem;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.07)">
      <h2 style="font-size:1.1rem;margin:0 0 1.25rem">🖼️ Photo de profil</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_avatar">
        <div class="form-group">
          <label class="form-label">Choisir une image (JPG, PNG, max 2MB)</label>
          <input type="file" name="avatar" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn--primary btn--block">Mettre à jour la photo</button>
      </form>
    </div>

    <!-- Messagerie rapide -->
    <div style="background:#fff;border-radius:.75rem;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.07)">
      <h2 style="font-size:1.1rem;margin:0 0 1.25rem">✉️ Messagerie</h2>
      <p style="color:#666;font-size:.9rem;margin-bottom:1rem">
        Contactez les enseignants et l'administration directement via la messagerie interne.
      </p>
      <a href="<?= BASE_PATH ?>/messages.php" class="btn btn--primary btn--block">
        💬 Ouvrir la messagerie
        <?php if ($nbMessagesNonLus > 0): ?>
          <span style="background:#e74c3c;color:#fff;border-radius:999px;padding:1px 7px;font-size:.8rem;margin-left:.4rem"><?= $nbMessagesNonLus ?></span>
        <?php endif; ?>
      </a>
    </div>

  </div>
</main>

<?php require_once __DIR__ . '/../views/shared/footer.php'; ?>
