<?php
// src/controllers/AuthController.php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../helpers/functions.php';

class AuthController {
    private User $user;

    public function __construct() {
        $this->user = new User();
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_verify()) redirect('/login.php', 'Token de sécurité invalide.', 'error');

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            redirect('/login.php', 'Veuillez remplir tous les champs.', 'error');
        }

        $userRecord = $this->user->findByEmail($email);
        if (!$userRecord || !password_verify($password, $userRecord['mot_de_passe'])) {
            redirect('/login.php', 'Email ou mot de passe incorrect.', 'error');
        }

        AuthMiddleware::login($userRecord);
        $this->user->updateLastLogin($userRecord['id']);
        redirect('/dashboard.php', 'Bienvenue, ' . $userRecord['prenom'] . ' !');
    }

    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        if (!csrf_verify()) redirect('/register.php', 'Token de sécurité invalide.', 'error');

        $nom       = trim($_POST['nom'] ?? '');
        $prenom    = trim($_POST['prenom'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $password  = $_POST['password'] ?? '';
        $confirm   = $_POST['password_confirm'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            redirect('/register.php', 'Veuillez remplir tous les champs obligatoires.', 'error');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirect('/register.php', 'Adresse email invalide.', 'error');
        }
        if (strlen($password) < 8) {
            redirect('/register.php', 'Le mot de passe doit contenir au moins 8 caractères.', 'error');
        }
        if ($password !== $confirm) {
            redirect('/register.php', 'Les mots de passe ne correspondent pas.', 'error');
        }
        if ($this->user->emailExists($email)) {
            redirect('/register.php', 'Cet email est déjà utilisé.', 'error');
        }

        $this->user->create([
            'nom'          => $nom,
            'prenom'       => $prenom,
            'email'        => $email,
            'telephone'    => $telephone ?: null,
            'mot_de_passe' => $password,
            'role'         => 'etudiant',
        ]);
        redirect('/login.php', 'Compte créé avec succès ! Vous pouvez vous connecter.');
    }

    public function logout(): void {
        AuthMiddleware::logout();
    }
}
