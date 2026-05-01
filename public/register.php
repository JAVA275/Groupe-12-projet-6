<?php
include("../config/db.php");

$message      = "";
$message_type = "";

if(isset($_POST['register'])){

    $nom      = trim($_POST['nom']);
    $role     = $_POST['role'];
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Rôle admin interdit via l'inscription publique
    if($role === 'admin'){
        $message      = "L'inscription en tant qu'administrateur n'est pas autorisée ici.";
        $message_type = "error";
    } elseif($password !== $confirm){
        $message      = "Les mots de passe ne correspondent pas. Veuillez réessayer.";
        $message_type = "error";
    } elseif(strlen($password) < 6){
        $message      = "Le mot de passe doit contenir au moins 6 caractères.";
        $message_type = "error";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $message      = "Cette adresse email est déjà utilisée.";
            $message_type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (nom, role, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nom, $role, $email, $hashed);

            if($stmt->execute()){
                $message      = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                $message_type = "success";
            } else {
                $message      = "Une erreur est survenue. Veuillez réessayer.";
                $message_type = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — Gestion des Examens</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="auth-wrapper">
<div class="auth-card">

    <div class="auth-card-header">
        <div class="icon">📝</div>
        <h2>Créer un compte</h2>
        <p>Faculté des Sciences · Gestion des Examens et Corrections</p>
    </div>

    <div class="auth-card-body">

        <?php if($message != ""): ?>
        <div class="alert alert-<?php echo $message_type === 'error' ? 'error' : 'success'; ?>">
            <?php echo $message_type === 'error' ? '✕' : '✓'; ?>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php if($message_type === 'success'): ?>
        <p style="text-align:center; margin-bottom:16px;">
            <a href="login.php" class="btn btn-gold">← Se connecter</a>
        </p>
        <?php endif; ?>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label class="form-label">Nom complet</label>
                <input type="text" name="nom" placeholder=" ex:Bello Goukla" required
                       value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Profil</label>
                <select name="role" required>
                    <option value="">Sélectionner votre rôle</option>
                    <option value="etudiant"   <?php echo (isset($_POST['role']) && $_POST['role']=='etudiant')   ? 'selected' : ''; ?>>🎓 Étudiant</option>
                    <option value="enseignant" <?php echo (isset($_POST['role']) && $_POST['role']=='enseignant') ? 'selected' : ''; ?>>👨‍🏫 Enseignant</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <input type="email" name="email" placeholder="votre@email.cm" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" placeholder="Minimum 6 caractères" required>
            </div>

            <div class="form-group">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       placeholder="Répétez votre mot de passe" required>
                <div id="pwd-match-msg" style="font-size:0.82rem; margin-top:5px;"></div>
            </div>

            <button class="btn btn-gold btn-lg" name="register" type="submit">
                Créer mon compte
            </button>

        </form>

        <div class="divider"></div>

        <p style="text-align:center; font-size:0.88rem; color:var(--text-muted);">
            Déjà inscrit ?
            <a href="login.php" style="font-weight:600;">Se connecter</a>
        </p>

    </div>
</div>
</div>

<script>
// Vérification en temps réel des mots de passe
const pwd     = document.getElementById('password');
const confirm = document.getElementById('confirm_password');
const msg     = document.getElementById('pwd-match-msg');

function checkMatch(){
    if(confirm.value === ''){
        msg.textContent = '';
        return;
    }
    if(pwd.value === confirm.value){
        msg.style.color = '#27ae60';
        msg.textContent = '✓ Les mots de passe correspondent';
    } else {
        msg.style.color = '#c0392b';
        msg.textContent = '✕ Les mots de passe ne correspondent pas';
    }
}
pwd.addEventListener('input',     checkMatch);
confirm.addEventListener('input', checkMatch);
</script>

</body>
</html>
