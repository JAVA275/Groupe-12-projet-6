<?php
// public/actions/toggle-user.php
require_once __DIR__ . '/../../src/controllers/AdminController.php';
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) (new AdminController())->toggleUtilisateur($id);
else header('Location: ' . BASE_PATH . '/admin/utilisateurs.php');
