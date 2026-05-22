<?php
// src/models/Evaluation.php

require_once __DIR__ . '/../config/database.php';

class Evaluation {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function noter(int $userId, int $sujetId, int $note): bool {
        $note = max(1, min(5, $note));
        $stmt = $this->db->prepare(
            "INSERT INTO evaluations (utilisateur_id, sujet_id, note) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE note = VALUES(note)"
        );
        return $stmt->execute([$userId, $sujetId, $note]);
    }

    public function getUserNote(int $userId, int $sujetId): ?int {
        $stmt = $this->db->prepare("SELECT note FROM evaluations WHERE utilisateur_id = ? AND sujet_id = ?");
        $stmt->execute([$userId, $sujetId]);
        $row = $stmt->fetch();
        return $row ? (int)$row['note'] : null;
    }

    public function getMoyenne(int $sujetId): float {
        $stmt = $this->db->prepare("SELECT AVG(note) FROM evaluations WHERE sujet_id = ?");
        $stmt->execute([$sujetId]);
        return round((float)$stmt->fetchColumn(), 1);
    }
}
