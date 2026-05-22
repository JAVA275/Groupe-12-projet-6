<?php
// src/models/Message.php
require_once __DIR__ . '/../config/database.php';

class Message {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function envoyer(int $expediteurId, int $destinataireId, string $sujet, string $contenu): int {
        $stmt = $this->db->prepare(
            "INSERT INTO messages (expediteur_id, destinataire_id, sujet, contenu) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$expediteurId, $destinataireId, $sujet, $contenu]);
        return (int)$this->db->lastInsertId();
    }

    public function getRecus(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.nom AS exp_nom, u.prenom AS exp_prenom, u.role AS exp_role
             FROM messages m
             JOIN utilisateurs u ON m.expediteur_id = u.id
             WHERE m.destinataire_id = ?
             ORDER BY m.date_envoi DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getEnvoyes(int $userId): array {
        $stmt = $this->db->prepare(
            "SELECT m.*, u.nom AS dest_nom, u.prenom AS dest_prenom, u.role AS dest_role
             FROM messages m
             JOIN utilisateurs u ON m.destinataire_id = u.id
             WHERE m.expediteur_id = ?
             ORDER BY m.date_envoi DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT m.*,
                    e.nom AS exp_nom, e.prenom AS exp_prenom, e.role AS exp_role, e.telephone AS exp_tel,
                    d.nom AS dest_nom, d.prenom AS dest_prenom, d.role AS dest_role
             FROM messages m
             JOIN utilisateurs e ON m.expediteur_id = e.id
             JOIN utilisateurs d ON m.destinataire_id = d.id
             WHERE m.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function marquerLu(int $id, int $userId): void {
        $stmt = $this->db->prepare(
            "UPDATE messages SET lu = 1 WHERE id = ? AND destinataire_id = ?"
        );
        $stmt->execute([$id, $userId]);
    }

    public function countNonLus(int $userId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM messages WHERE destinataire_id = ? AND lu = 0"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    public function supprimer(int $id, int $userId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM messages WHERE id = ? AND (expediteur_id = ? OR destinataire_id = ?)"
        );
        return $stmt->execute([$id, $userId, $userId]);
    }

    public function getAdminsEnseignants(): array {
        $stmt = $this->db->query(
            "SELECT id, nom, prenom, role, telephone FROM utilisateurs
             WHERE role IN ('admin','enseignant') AND is_active = 1
             ORDER BY role, nom"
        );
        return $stmt->fetchAll();
    }
}
