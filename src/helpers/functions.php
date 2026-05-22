<?php
if (!defined('BASE_PATH')) define('BASE_PATH', '/projet_ecole/public');
// src/helpers/functions.php


function uploadFichier(array $file, string $dossier = 'uploads'): string {
    $allowedTypes = ['application/pdf', 'application/msword',
                     'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $maxSize = 10 * 1024 * 1024; // 10MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Erreur lors de l'upload du fichier.");
    }
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Format non autorisé. Seuls PDF et DOCX sont acceptés.");
    }
    if ($file['size'] > $maxSize) {
        throw new Exception("Fichier trop volumineux. Maximum 10MB.");
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nom = uniqid('doc_', true) . '.' . strtolower($ext);
    $chemin = dirname(__DIR__, 2) . '/public/' . $dossier . '/' . $nom;

    if (!is_dir(dirname($chemin))) {
        mkdir(dirname($chemin), 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $chemin)) {
        throw new Exception("Impossible de sauvegarder le fichier.");
    }

    return $dossier . '/' . $nom;
}

function redirect(string $url, string $message = '', string $type = 'success'): void {
    if ($message) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
    // Ajouter le base path si l'URL commence par /
    if (str_starts_with($url, '/')) {
        $url = BASE_PATH . $url;
    }
    header("Location: $url");
    exit;
}

function getFlash(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function niveaux(): array {
    return ['Licence 1', 'Licence 2', 'Licence 3', 'Master 1', 'Master 2'];
}

function sessions(): array {
    return ['Normale', 'Rattrapage'];
}

function anneesAcademiques(): array {
    $annees = [];
    $year = (int)date('Y');
    for ($i = $year; $i >= $year - 10; $i--) {
        $annees[] = $i . '-' . ($i + 1);
    }
    return $annees;
}

function formatDate(string $date): string {
    return date('d/m/Y', strtotime($date));
}

function formatDateHeure(string $date): string {
    return date('d/m/Y à H:i', strtotime($date));
}

function etoiles(float $note): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= round($note) ? '★' : '☆';
    }
    return $html;
}

function paginate(int $total, int $perPage, int $current, string $url): string {
    $totalPages = (int)ceil($total / $perPage);
    if ($totalPages <= 1) return '';

    $html = '<nav class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i === $current ? ' active' : '';
        $sep = str_contains($url, '?') ? '&' : '?';
        $html .= "<a href=\"{$url}{$sep}page={$i}\" class=\"page-btn{$active}\">{$i}</a>";
    }
    $html .= '</nav>';
    return $html;
}
