<?php
// src/models/Correction.php

require_once __DIR__ . '/../config/database.php';

class Correction {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getBySujet(int $sujetId): array {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
             FROM corrections c
             LEFT JOIN utilisateurs u ON c.auteur_id = u.id
             WHERE c.sujet_id = ?
             ORDER BY c.date_ajout DESC"
        );
        $stmt->execute([$sujetId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM corrections WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO corrections (sujet_id, auteur_id, fichier, description) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$data['sujet_id'], $data['auteur_id'], $data['fichier'], $data['description'] ?? null]);
        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id, int $auteurId, string $role = 'enseignant'): bool {
        $corr = $this->findById($id);
        if (!$corr) return false;
        if ($role !== 'admin' && $corr['auteur_id'] !== $auteurId) return false;
        if (file_exists(dirname(__DIR__, 2) . '/public/' . $corr['fichier'])) {
            unlink(dirname(__DIR__, 2) . '/public/' . $corr['fichier']);
        }
        $stmt = $this->db->prepare("DELETE FROM corrections WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotal(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM corrections")->fetchColumn();
    }
}
