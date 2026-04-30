<?php
// public/admin/utilisateurs.php
require_once __DIR__ . '/../../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/helpers/functions.php';
require_once __DIR__ . '/../../src/models/User.php';
require_once __DIR__ . '/../../src/controllers/AdminController.php';

new AdminController(); // check admin
$user = AuthMiddleware::currentUser();
$userModel = new User();

$roleFilter = $_GET['role'] ?? '';
$utilisateurs = $userModel->getAll($roleFilter ?: null);

$pageTitle = 'Gestion des utilisateurs';
include __DIR__ . '/../../views/shared/header.php';
?>
<main>
<div class="page-hero">
  <div class="container">
    <a href="<?= BASE_PATH ?>/admin/dashboard.php" style="color:var(--slate);text-decoration:none;font-size:.85rem">← Admin</a>
    <h1 class="page-hero__title" style="margin-top:.5rem">👥 Gestion des utilisateurs</h1>
    <p class="page-hero__sub"><?= count($utilisateurs) ?> utilisateur<?= count($utilisateurs) > 1 ? 's' : '' ?></p>
  </div>
</div>

<div class="container page-content">
  <div class="grid grid--2" style="gap:2rem;align-items:start">
    <!-- Add teacher form -->
    <div class="card">
      <div class="card-header"><span class="card-title">➕ Ajouter un enseignant</span></div>
      <div class="card-body">
        <p style="font-size:.85rem;color:var(--slate);margin-bottom:1rem">
          Seul l'administrateur peut créer un compte enseignant.
        </p>
        <form action="<?= BASE_PATH ?>/actions/add-teacher.php" method="POST">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <div class="form-group">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" placeholder="Java" required>
          </div>
          <div class="form-group">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" placeholder="Samuel" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="java237@gmail.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">📞 Téléphone </label>
            <input type="tel" name="telephone" class="form-control" placeholder="+237 6XX XXX XXX">
          </div>
          <div class="form-group">
            <label class="form-label">Mot de passe provisoire</label>
            <div style="position:relative">
              <input type="password" id="teacher_pwd" name="password" class="form-control" placeholder="Min. 8 caractères" required minlength="8">
              <button type="button" class="toggle-password" data-target="teacher_pwd"
                      style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1rem;line-height:1">👁️</button>
            </div>
          </div>
          <button type="submit" class="btn btn--primary btn--block">Créer le compte enseignant</button>
        </form>
      </div>
    </div>

    <!-- Users list -->
    <div>
      <!-- Filter tabs -->
      <div style="display:flex;gap:.5rem;margin-bottom:1.25rem">
        <a href="<?= BASE_PATH ?>/admin/utilisateurs.php" class="btn <?= !$roleFilter ? 'btn--primary' : 'btn--outline' ?> btn--sm">Tous</a>
        <a href="<?= BASE_PATH ?>/admin/utilisateurs.php?role=etudiant" class="btn <?= $roleFilter==='etudiant' ? 'btn--primary' : 'btn--outline' ?> btn--sm">Étudiants</a>
        <a href="<?= BASE_PATH ?>/admin/utilisateurs.php?role=enseignant" class="btn <?= $roleFilter==='enseignant' ? 'btn--primary' : 'btn--outline' ?> btn--sm">Enseignants</a>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($utilisateurs as $u): ?>
            <tr>
              <td>
                <strong><?= e($u['nom'].' '.$u['prenom']) ?></strong>
                <?php if(!empty($u['telephone'])): ?><div style="font-size:.75rem;color:#27ae60">📞 <?= e($u['telephone']) ?></div><?php endif; ?>
                <div style="font-size:.75rem;color:var(--slate)"><?= formatDate($u['date_creation']) ?></div>
              </td>
              <td style="font-size:.85rem"><?= e($u['email']) ?></td>
              <td><span class="badge badge--<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
              <td>
                <span style="font-size:.8rem;font-weight:600;color:<?= $u['is_active'] ? 'var(--success)' : 'var(--danger)' ?>">
                  <?= $u['is_active'] ? '● Actif' : '● Inactif' ?>
                </span>
              </td>
              <td>
                <?php if ($u['role'] !== 'admin'): ?>
                <div style="display:flex;gap:.35rem">
                  <a href="<?= BASE_PATH ?>/actions/toggle-user.php?id=<?= $u['id'] ?>"
                     class="btn btn--outline btn--sm"
                     onclick="return confirm('Changer le statut de cet utilisateur ?')">
                     <?= $u['is_active'] ? '🔒 Désactiver' : '🔓 Activer' ?>
                  </a>
                  <a href="<?= BASE_PATH ?>/actions/delete-user.php?id=<?= $u['id'] ?>"
                     class="btn btn--danger btn--sm"
                     onclick="return confirm('Supprimer définitivement cet utilisateur et toutes ses données ?')">🗑️</a>
                </div>
                <?php else: ?>
                <span style="font-size:.78rem;color:var(--slate)">Admin principal</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($utilisateurs)): ?>
            <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--slate)">Aucun utilisateur</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</main>
<?php include __DIR__ . '/../../views/shared/footer.php'; ?>
