<?php
// src/controllers/SujetController.php

require_once __DIR__ . '/../models/Sujet.php';
require_once __DIR__ . '/../models/Correction.php';
require_once __DIR__ . '/../models/Commentaire.php';
require_once __DIR__ . '/../models/Evaluation.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../config/database.php';

class SujetController {
    private Sujet $sujet;
    private Correction $correction;
    private Commentaire $commentaire;
    private Evaluation $evaluation;

    public function __construct() {
        $this->sujet       = new Sujet();
        $this->correction  = new Correction();
        $this->commentaire = new Commentaire();
        $this->evaluation  = new Evaluation();
    }

    public function create(): void {
        AuthMiddleware::requireRole('enseignant', 'admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_verify()) redirect('/ajouter-sujet.php', 'Token invalide.', 'error');

        $user = AuthMiddleware::currentUser();
        try {
            if (empty($_FILES['fichier']['name'])) throw new Exception("Le fichier est obligatoire.");
            $fichier = uploadFichier($_FILES['fichier']);

            $id = $this->sujet->create([
                'titre'           => trim($_POST['titre']),
                'matiere_id'      => $_POST['matiere_id'] ?? null,
                'filiere_id'      => $_POST['filiere_id'] ?? null,
                'niveau'          => $_POST['niveau'],
                'session'         => $_POST['session'],
                'annee_academique'=> $_POST['annee_academique'],
                'description'     => trim($_POST['description'] ?? ''),
                'fichier'         => $fichier,
                'auteur_id'       => $user['id'],
            ]);
            redirect('/sujet.php?id=' . $id, 'Sujet publié avec succès !');
        } catch (Exception $e) {
            redirect('/ajouter-sujet.php', $e->getMessage(), 'error');
        }
    }

    public function update(int $id): void {
        AuthMiddleware::requireRole('enseignant', 'admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_verify()) redirect('/modifier-sujet.php?id='.$id, 'Token invalide.', 'error');

        $user = AuthMiddleware::currentUser();
        $data = [
            'titre'           => trim($_POST['titre']),
            'matiere_id'      => $_POST['matiere_id'] ?? null,
            'filiere_id'      => $_POST['filiere_id'] ?? null,
            'niveau'          => $_POST['niveau'],
            'session'         => $_POST['session'],
            'annee_academique'=> $_POST['annee_academique'],
            'description'     => trim($_POST['description'] ?? ''),
        ];

        if ($this->sujet->update($id, $data, $user['id'], $user['role'])) {
            redirect('/sujet.php?id='.$id, 'Sujet modifié avec succès !');
        } else {
            redirect('/modifier-sujet.php?id='.$id, 'Modification impossible.', 'error');
        }
    }

    public function delete(int $id): void {
        AuthMiddleware::requireRole('enseignant', 'admin');
        $user = AuthMiddleware::currentUser();
        if ($this->sujet->delete($id, $user['id'], $user['role'])) {
            redirect('/sujets.php', 'Sujet supprimé.');
        } else {
            redirect('/sujets.php', 'Suppression impossible.', 'error');
        }
    }

    public function addCorrection(int $sujetId): void {
        AuthMiddleware::requireRole('enseignant', 'admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_verify()) redirect('/sujet.php?id='.$sujetId, 'Token invalide.', 'error');

        $user = AuthMiddleware::currentUser();
        try {
            if (empty($_FILES['fichier']['name'])) throw new Exception("Le fichier est obligatoire.");
            $fichier = uploadFichier($_FILES['fichier']);
            $this->correction->create([
                'sujet_id'    => $sujetId,
                'auteur_id'   => $user['id'],
                'fichier'     => $fichier,
                'description' => trim($_POST['description'] ?? ''),
            ]);
            redirect('/sujet.php?id='.$sujetId, 'Correction ajoutée !');
        } catch (Exception $e) {
            redirect('/sujet.php?id='.$sujetId, $e->getMessage(), 'error');
        }
    }

    public function addCommentaire(int $sujetId): void {
        AuthMiddleware::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $user = AuthMiddleware::currentUser();
        $contenu = trim($_POST['contenu'] ?? '');
        if (empty($contenu)) redirect('/sujet.php?id='.$sujetId, 'Le commentaire est vide.', 'error');
        $this->commentaire->create($user['id'], $sujetId, $contenu);
        redirect('/sujet.php?id='.$sujetId, 'Commentaire ajouté !');
    }

    public function noter(int $sujetId): void {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::currentUser();
        $note = (int)($_POST['note'] ?? 0);
        if ($note < 1 || $note > 5) redirect('/sujet.php?id='.$sujetId, 'Note invalide.', 'error');
        $this->evaluation->noter($user['id'], $sujetId, $note);
        redirect('/sujet.php?id='.$sujetId, 'Note enregistrée !');
    }

    public function telecharger(int $sujetId): void {
        AuthMiddleware::requireLogin();
        $sujet = $this->sujet->findById($sujetId);
        if (!$sujet) { http_response_code(404); die('Sujet introuvable.'); }

        $path = dirname(__DIR__, 2) . '/public/' . $sujet['fichier'];
        if (!file_exists($path)) { http_response_code(404); die('Fichier introuvable.'); }

        $this->sujet->incrementTelechargements($sujetId);

        $user = AuthMiddleware::currentUser();
        $db = Database::getInstance();
        $db->prepare("INSERT INTO telechargements (utilisateur_id, sujet_id) VALUES (?, ?)")->execute([$user['id'], $sujetId]);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($sujet['fichier']) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function telechargerCorrection(int $corrId): void {
        AuthMiddleware::requireLogin();
        $corr = $this->correction->findById($corrId);
        if (!$corr) { http_response_code(404); die('Correction introuvable.'); }

        $path = dirname(__DIR__, 2) . '/public/' . $corr['fichier'];
        if (!file_exists($path)) { http_response_code(404); die('Fichier introuvable.'); }

        $user = AuthMiddleware::currentUser();
        $db = Database::getInstance();
        $db->prepare("INSERT INTO telechargements (utilisateur_id, correction_id) VALUES (?, ?)")->execute([$user['id'], $corrId]);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($corr['fichier']) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function getMatieres(): array {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM matieres ORDER BY nom")->fetchAll();
    }

    public function getFilieres(): array {
        $db = Database::getInstance();
        return $db->query("SELECT * FROM filieres ORDER BY nom")->fetchAll();
    }
}
