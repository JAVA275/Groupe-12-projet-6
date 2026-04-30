<?php
// public/actions/delete-user.php
require_once __DIR__ . '/../../src/controllers/AdminController.php';
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) (new AdminController())->supprimerUtilisateur($id);
else header('Location: ' . BASE_PATH . '/admin/utilisateurs.php');
