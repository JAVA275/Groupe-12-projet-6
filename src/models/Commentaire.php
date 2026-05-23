<?php
// src/models/Commentaire.php

require_once __DIR__ . '/../config/database.php';

class Commentaire {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getBySujet(int $sujetId): array {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.nom, u.prenom, u.role
             FROM commentaires c
             JOIN utilisateurs u ON c.utilisateur_id = u.id
             WHERE c.sujet_id = ?
             ORDER BY c.date_creation DESC"
        );
        $stmt->execute([$sujetId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, int $sujetId, string $contenu): int {
        $stmt = $this->db->prepare(
            "INSERT INTO commentaires (utilisateur_id, sujet_id, contenu) VALUES (?, ?, ?)"
        );
        $stmt->execute([$userId, $sujetId, htmlspecialchars($contenu, ENT_QUOTES, 'UTF-8')]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id, int $userId, string $role = 'etudiant'): bool {
        $check = $role === 'admin' ? '' : ' AND utilisateur_id = ' . $userId;
        $stmt = $this->db->prepare("DELETE FROM commentaires WHERE id = ?$check");
        return $stmt->execute([$id]);
    }

    public function countBySujet(int $sujetId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM commentaires WHERE sujet_id = ?");
        $stmt->execute([$sujetId]);
        return (int)$stmt->fetchColumn();
    }
}
