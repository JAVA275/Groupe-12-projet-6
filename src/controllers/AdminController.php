<?php
// src/controllers/AdminController.php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Sujet.php';
require_once __DIR__ . '/../models/Correction.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../config/database.php';

class AdminController {
    private User $user;
    private Sujet $sujet;

    public function __construct() {
        AuthMiddleware::requireRole('admin');
        $this->user  = new User();
        $this->sujet = new Sujet();
    }

    public function ajouterEnseignant(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_verify()) redirect('/admin/utilisateurs.php', 'Token invalide.', 'error');

        $nom       = trim($_POST['nom'] ?? '');
        $prenom    = trim($_POST['prenom'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $password  = $_POST['password'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            redirect('/admin/utilisateurs.php', 'Tous les champs obligatoires sont requis.', 'error');
        }
        if ($this->user->emailExists($email)) {
            redirect('/admin/utilisateurs.php', 'Cet email est déjà utilisé.', 'error');
        }

        $this->user->create([
            'nom'          => $nom,
            'prenom'       => $prenom,
            'email'        => $email,
            'telephone'    => $telephone ?: null,
            'mot_de_passe' => $password,
            'role'         => 'enseignant',
        ]);
        redirect('/admin/utilisateurs.php', 'Enseignant ajouté avec succès !');
    }

    public function toggleUtilisateur(int $id): void {
        $u = $this->user->findById($id);
        if (!$u) redirect('/admin/utilisateurs.php', 'Utilisateur introuvable.', 'error');
        $this->user->update($id, ['is_active' => $u['is_active'] ? 0 : 1]);
        redirect('/admin/utilisateurs.php', 'Statut mis à jour.');
    }

    public function supprimerUtilisateur(int $id): void {
        $this->user->delete($id);
        redirect('/admin/utilisateurs.php', 'Utilisateur supprimé.');
    }

    public function getDashboardData(): array {
        $userStats  = $this->user->getStats();
        $sujetStats = $this->sujet->getStats();
        $db = Database::getInstance();

        $corrTotal = (int)$db->query("SELECT COUNT(*) FROM corrections")->fetchColumn();
        $dlTotal   = (int)$db->query("SELECT COUNT(*) FROM telechargements")->fetchColumn();

        $activiteRecente = $db->query(
            "SELECT 'sujet' AS type, s.titre AS label, u.nom, u.prenom, s.date_publication AS date
             FROM sujets s JOIN utilisateurs u ON s.auteur_id = u.id
             UNION ALL
             SELECT 'correction', CONCAT('Correction sujet #', c.sujet_id), u.nom, u.prenom, c.date_ajout
             FROM corrections c JOIN utilisateurs u ON c.auteur_id = u.id
             ORDER BY date DESC LIMIT 10"
        )->fetchAll();

        return [
            'nb_etudiants'       => $userStats['etudiant'],
            'nb_enseignants'     => $userStats['enseignant'],
            'nb_sujets'          => (int)$sujetStats['total'],
            'nb_corrections'     => $corrTotal,
            'nb_telechargements' => $dlTotal,
            'activite_recente'   => $activiteRecente,
            'sujets_populaires'  => $this->sujet->getPopulaires(5),
        ];
    }
}
