<?php
session_start();
include("../config/db.php");

$message      = "";
$message_type = "";

if(isset($_POST['login'])){
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user']    = $user;
            $_SESSION['role']    = $user['role'];
            $_SESSION['user_id'] = $user['id'];
            session_regenerate_id(true);
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            $message      = "Mot de passe incorrect. Veuillez réessayer.";
            $message_type = "error";
        }
    } else {
        $message      = "Aucun compte trouvé avec cet email.";
        $message_type = "error";
    }
}

// Vérifier si un admin existe (pour afficher le lien d'initialisation si besoin)
$admin_exists = false;
$chk = $conn->query("SELECT id FROM users WHERE role='admin' LIMIT 1");
if($chk && $chk->num_rows > 0) $admin_exists = true;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Gestion des Examens</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="auth-wrapper">
<div class="auth-card">

    <div class="auth-card-header">
        <div class="icon">🔐</div>
        <h2>Connexion</h2>
        <p>Faculté des Sciences · Gestion des Examens et Corrections</p>
    </div>

    <div class="auth-card-body">

        <?php if($message != ""): ?>
        <div class="alert alert-<?php echo $message_type === 'error' ? 'error' : 'success'; ?>">
            <?php echo $message_type === 'error' ? '✕' : '✓'; ?>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Adresse email</label>
                <input type="email" name="email" placeholder="votre@email.cm" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button class="btn btn-gold btn-lg" name="login" type="submit">
                Se connecter
            </button>
        </form>

        <div class="divider"></div>

        <p style="text-align:center; font-size:0.88rem; color:var(--text-muted);">
            Pas encore de compte ?
            <a href="register.php" style="font-weight:600;">Créer un compte</a>
        </p>

        <?php if(!$admin_exists): ?>
        <div style="margin-top:18px; padding:14px 16px; background:#fff3cd; border-radius:8px; border-left:4px solid #c9a84c; font-size:0.85rem;">
            Aucun administrateur configuré.<br>
            <a href="../generate_admin.php" style="font-weight:700; color:#1a2a4a;">
                 Initialiser l'administrateur principal
            </a>
        </div>
        <?php endif; ?>

    </div>
</div>
</div>

</body>
</html>
