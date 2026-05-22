<?php
// src/models/Sujet.php

require_once __DIR__ . '/../config/database.php';

class Sujet {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(array $filters = [], int $page = 1, int $perPage = 12): array {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['matiere_id'])) { $where[] = 's.matiere_id = ?'; $params[] = $filters['matiere_id']; }
        if (!empty($filters['filiere_id'])) { $where[] = 's.filiere_id = ?'; $params[] = $filters['filiere_id']; }
        if (!empty($filters['niveau']))     { $where[] = 's.niveau = ?';      $params[] = $filters['niveau']; }
        if (!empty($filters['session']))    { $where[] = 's.session = ?';     $params[] = $filters['session']; }
        if (!empty($filters['annee']))      { $where[] = 's.annee_academique = ?'; $params[] = $filters['annee']; }
        if (!empty($filters['auteur_id']))  { $where[] = 's.auteur_id = ?';   $params[] = $filters['auteur_id']; }
        if (!empty($filters['q'])) {
            $where[] = '(s.titre LIKE ? OR m.nom LIKE ?)';
            $params[] = '%'.$filters['q'].'%';
            $params[] = '%'.$filters['q'].'%';
        }

        $whereStr = implode(' AND ', $where);
        $offset   = ($page - 1) * $perPage;

        $sql = "SELECT s.*, m.nom AS matiere_nom, f.nom AS filiere_nom, f.code AS filiere_code,
                       u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                       AVG(e.note) AS note_moyenne, COUNT(DISTINCT e.id) AS nb_evaluations,
                       COUNT(DISTINCT c.id) AS nb_corrections
                FROM sujets s
                LEFT JOIN matieres m ON s.matiere_id = m.id
                LEFT JOIN filieres f ON s.filiere_id = f.id
                LEFT JOIN utilisateurs u ON s.auteur_id = u.id
                LEFT JOIN evaluations e ON s.id = e.sujet_id
                LEFT JOIN corrections c ON s.id = c.sujet_id
                WHERE $whereStr
                GROUP BY s.id
                ORDER BY s.date_publication DESC
                LIMIT $perPage OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['matiere_id'])) { $where[] = 's.matiere_id = ?'; $params[] = $filters['matiere_id']; }
        if (!empty($filters['filiere_id'])) { $where[] = 's.filiere_id = ?'; $params[] = $filters['filiere_id']; }
        if (!empty($filters['niveau']))     { $where[] = 's.niveau = ?';     $params[] = $filters['niveau']; }
        if (!empty($filters['session']))    { $where[] = 's.session = ?';    $params[] = $filters['session']; }
        if (!empty($filters['annee']))      { $where[] = 's.annee_academique = ?'; $params[] = $filters['annee']; }
        if (!empty($filters['auteur_id']))  { $where[] = 's.auteur_id = ?';  $params[] = $filters['auteur_id']; }
        if (!empty($filters['q'])) {
            $where[] = '(s.titre LIKE ? OR m.nom LIKE ?)';
            $params[] = '%'.$filters['q'].'%';
            $params[] = '%'.$filters['q'].'%';
        }

        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT s.id) FROM sujets s LEFT JOIN matieres m ON s.matiere_id = m.id WHERE $whereStr"
        );
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT s.*, m.nom AS matiere_nom, f.nom AS filiere_nom, f.code AS filiere_code,
                    u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                    AVG(e.note) AS note_moyenne, COUNT(DISTINCT e.id) AS nb_evaluations,
                    COUNT(DISTINCT c.id) AS nb_corrections
             FROM sujets s
             LEFT JOIN matieres m ON s.matiere_id = m.id
             LEFT JOIN filieres f ON s.filiere_id = f.id
             LEFT JOIN utilisateurs u ON s.auteur_id = u.id
             LEFT JOIN evaluations e ON s.id = e.sujet_id
             LEFT JOIN corrections c ON s.id = c.sujet_id
             WHERE s.id = ?
             GROUP BY s.id"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO sujets (titre, matiere_id, filiere_id, niveau, session, annee_academique, description, fichier, auteur_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['titre'],
            $data['matiere_id'] ?: null,
            $data['filiere_id'] ?: null,
            $data['niveau'],
            $data['session'],
            $data['annee_academique'],
            $data['description'] ?? null,
            $data['fichier'],
            $data['auteur_id'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data, int $auteurId, string $role = 'enseignant'): bool {
        $check = $role === 'admin' ? '' : ' AND auteur_id = ' . $auteurId;
        $stmt  = $this->db->prepare(
            "UPDATE sujets SET titre=?, matiere_id=?, filiere_id=?, niveau=?, session=?, annee_academique=?, description=?
             WHERE id=?$check"
        );
        return $stmt->execute([
            $data['titre'], $data['matiere_id'] ?: null, $data['filiere_id'] ?: null,
            $data['niveau'], $data['session'], $data['annee_academique'], $data['description'] ?? null, $id
        ]);
    }

    public function delete(int $id, int $auteurId, string $role = 'enseignant'): bool {
        $check = $role === 'admin' ? '' : ' AND auteur_id = ' . $auteurId;
        $sujet = $this->findById($id);
        if ($sujet && file_exists(dirname(__DIR__, 2) . '/public/' . $sujet['fichier'])) {
            unlink(dirname(__DIR__, 2) . '/public/' . $sujet['fichier']);
        }
        $stmt = $this->db->prepare("DELETE FROM sujets WHERE id=?$check");
        return $stmt->execute([$id]);
    }

    /**
     * Incrémente les vues UNIQUEMENT si cet utilisateur n'a pas encore vu ce sujet.
     * Utilise la table vues_uniques pour le tracking.
     */
    public function incrementVuesUnique(int $sujetId, int $userId): void {
        // Créer la table si elle n'existe pas encore
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS vues_uniques (
                utilisateur_id INT NOT NULL,
                sujet_id INT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (utilisateur_id, sujet_id)
            ) ENGINE=InnoDB"
        );

        // Vérifier si déjà vu
        $stmt = $this->db->prepare(
            "SELECT 1 FROM vues_uniques WHERE utilisateur_id = ? AND sujet_id = ?"
        );
        $stmt->execute([$userId, $sujetId]);

        if (!$stmt->fetch()) {
            // Première visite : enregistrer et incrémenter
            $this->db->prepare(
                "INSERT IGNORE INTO vues_uniques (utilisateur_id, sujet_id) VALUES (?, ?)"
            )->execute([$userId, $sujetId]);

            $this->db->prepare(
                "UPDATE sujets SET nb_vues = nb_vues + 1 WHERE id = ?"
            )->execute([$sujetId]);
        }
        // Si déjà vu → on ne fait rien
    }

    public function incrementTelechargements(int $id): void {
        $this->db->prepare("UPDATE sujets SET nb_telechargements = nb_telechargements + 1 WHERE id = ?")->execute([$id]);
    }

    public function getStats(): array {
        $stmt = $this->db->query("SELECT COUNT(*) as total, SUM(nb_telechargements) as total_dl, SUM(nb_vues) as total_vues FROM sujets");
        return $stmt->fetch();
    }

    public function getAnnees(): array {
        $stmt = $this->db->query("SELECT DISTINCT annee_academique FROM sujets ORDER BY annee_academique DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRecents(int $limit = 6): array {
        $stmt = $this->db->prepare(
            "SELECT s.*, m.nom AS matiere_nom, f.nom AS filiere_nom, f.code AS filiere_code,
                    u.nom AS auteur_nom, u.prenom AS auteur_prenom
             FROM sujets s
             LEFT JOIN matieres m ON s.matiere_id = m.id
             LEFT JOIN filieres f ON s.filiere_id = f.id
             LEFT JOIN utilisateurs u ON s.auteur_id = u.id
             ORDER BY s.date_publication DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getPopulaires(int $limit = 5): array {
        $stmt = $this->db->prepare(
            "SELECT s.*, m.nom AS matiere_nom, f.nom AS filiere_nom
             FROM sujets s
             LEFT JOIN matieres m ON s.matiere_id = m.id
             LEFT JOIN filieres f ON s.filiere_id = f.id
             ORDER BY s.nb_telechargements DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
