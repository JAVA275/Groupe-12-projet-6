<?php
// public/admin/dashboard.php
require_once __DIR__ . '/../../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/helpers/functions.php';
require_once __DIR__ . '/../../src/controllers/AdminController.php';

$ctrl = new AdminController(); // requires admin
$user = AuthMiddleware::currentUser();
$data = $ctrl->getDashboardData();

$pageTitle = 'Administration';
include __DIR__ . '/../../views/shared/header.php';
?>
<main>
<div class="page-hero">
  <div class="container">
    <h1 class="page-hero__title">⚙️ Tableau de bord — Administration</h1>
  </div>
</div>

<div class="container page-content">
  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card" data-icon="🎓">
      <span class="stat-card__label">Étudiants</span>
      <span class="stat-card__value"><?= $data['nb_etudiants'] ?></span>
    </div>
    <div class="stat-card" data-icon="👤">
      <span class="stat-card__label">Enseignants</span>
      <span class="stat-card__value"><?= $data['nb_enseignants'] ?></span>
    </div>
    <div class="stat-card" data-icon="📄">
      <span class="stat-card__label">Sujets</span>
      <span class="stat-card__value"><?= $data['nb_sujets'] ?></span>
    </div>
    <div class="stat-card" data-icon="📝">
      <span class="stat-card__label">Corrections</span>
      <span class="stat-card__value"><?= $data['nb_corrections'] ?></span>
    </div>
    <div class="stat-card" data-icon="⬇️">
      <span class="stat-card__label">Téléchargements</span>
      <span class="stat-card__value"><?= $data['nb_telechargements'] ?></span>
    </div>
  </div>

  <div class="grid grid--2" style="gap:2rem;align-items:start">
    <!-- Activité récente -->
    <div class="card">
      <div class="card-header"><span class="card-title">Activité récente</span></div>
      <div class="card-body" style="padding:0">
        <?php if (empty($data['activite_recente'])): ?>
        <p style="padding:1.5rem;color:var(--slate);text-align:center">Aucune activité</p>
        <?php else: ?>
        <?php foreach ($data['activite_recente'] as $act): ?>
        <div style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1.25rem;border-bottom:1px solid #f0f0f0">
          <span style="font-size:1.1rem"><?= $act['type'] === 'sujet' ? '📄' : '📝' ?></span>
          <div style="flex:1;min-width:0">
            <div style="font-size:.875rem;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              <?= e($act['label']) ?>
            </div>
            <div style="font-size:.75rem;color:var(--slate)">
              Par <?= e($act['prenom'].' '.$act['nom']) ?> · <?= formatDate($act['date']) ?>
            </div>
          </div>
          <span class="badge badge--<?= $act['type'] === 'sujet' ? 'niveau' : 'correction' ?>"><?= ucfirst($act['type']) ?></span>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Sujets populaires + liens rapides -->
    <div style="display:flex;flex-direction:column;gap:1.25rem">
      <div class="card">
        <div class="card-header"><span class="card-title">Liens rapides</span></div>
        <div class="card-body">
          <div style="display:flex;flex-direction:column;gap:.5rem">
            <a href="<?= BASE_PATH ?>/admin/utilisateurs.php" class="btn btn--outline btn--block">👥 Gérer les utilisateurs</a>
            <a href="<?= BASE_PATH ?>/sujets.php" class="btn btn--outline btn--block">📄 Gérer les sujets</a>
            <a href="<?= BASE_PATH ?>/ajouter-sujet.php" class="btn btn--primary btn--block">+ Publier un sujet</a>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><span class="card-title">🔥 Sujets populaires</span></div>
        <div class="card-body" style="padding:0">
          <?php foreach ($data['sujets_populaires'] as $s): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;padding:.8rem 1.25rem;border-bottom:1px solid #f5f5f5">
            <a href="<?= BASE_PATH ?>/sujet.php?id=<?= $s['id'] ?>" style="font-size:.875rem;color:var(--navy);text-decoration:none;font-weight:500">
              <?= e(mb_strimwidth($s['titre'],0,50,'…')) ?>
            </a>
            <span style="font-size:.78rem;color:var(--slate)">⬇️ <?= $s['nb_telechargements'] ?></span>
          </div>
          <?php endforeach; ?>
          <?php if (empty($data['sujets_populaires'])): ?>
          <p style="padding:1rem;text-align:center;color:var(--slate)">Aucun sujet</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</main>
<?php include __DIR__ . '/../../views/shared/footer.php'; ?>
