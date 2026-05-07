<?php
// public/actions/delete-sujet.php
require_once __DIR__ . '/../../src/controllers/SujetController.php';
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) (new SujetController())->delete($id);
else header('Location: ' . BASE_PATH . '/sujets.php');
