<?php
// public/actions/send-message.php
require_once __DIR__ . '/../../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../../src/helpers/functions.php';
require_once __DIR__ . '/../../src/models/Message.php';

AuthMiddleware::requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/messages.php', '', 'error');
}
if (!csrf_verify()) {
    redirect('/messages.php', 'Token de sécurité invalide.', 'error');
}

$user           = AuthMiddleware::currentUser();
$destinataireId = (int)($_POST['destinataire_id'] ?? 0);
$sujet          = trim($_POST['sujet'] ?? '');
$contenu        = trim($_POST['contenu'] ?? '');

if (!$destinataireId || empty($sujet) || empty($contenu)) {
    redirect('/messages.php', 'Veuillez remplir tous les champs.', 'error');
}
if ($destinataireId === $user['id']) {
    redirect('/messages.php', 'Vous ne pouvez pas vous envoyer un message à vous-même.', 'error');
}

$msgModel = new Message();
$msgModel->envoyer($user['id'], $destinataireId, $sujet, $contenu);
redirect('/messages.php?tab=envoyes', 'Message envoyé avec succès !');
