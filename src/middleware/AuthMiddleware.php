<?php
// src/middleware/AuthMiddleware.php

if (!defined('BASE_PATH')) define('BASE_PATH', '/projet_ecole/public');

class AuthMiddleware {

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn(): bool {
        self::start();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function requireLogin(): void {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_PATH . '/login.php');
            exit;
        }
    }

    public static function requireRole(string ...$roles): void {
        self::requireLogin();
        if (!in_array($_SESSION['user_role'] ?? '', $roles)) {
            header('Location: ' . BASE_PATH . '/dashboard.php?error=access_denied');
            exit;
        }
    }

    public static function currentUser(): ?array {
        self::start();
        if (!self::isLoggedIn()) return null;
        return [
            'id'     => $_SESSION['user_id'],
            'nom'    => $_SESSION['user_nom'],
            'prenom' => $_SESSION['user_prenom'],
            'email'  => $_SESSION['user_email'],
            'role'   => $_SESSION['user_role'],
        ];
    }

    public static function login(array $user): void {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_nom']    = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['user_role']   = $user['role'];
    }

    public static function logout(): void {
        self::start();
        session_unset();
        session_destroy();
        header('Location: ' . BASE_PATH . '/login.php');
        exit;
    }
}
