<?php
session_start();
require_once __DIR__ . '/db.php';

if (isset($_SESSION['user_id']) && !isset($_GET['redirected'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    try {
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE email = ?");
        $stmt->execute([$email]);
        $etudiant = $stmt->fetch();

        if ($etudiant) {
            if ($etudiant['est_active'] == 0) {
                $message = "Votre compte n'est pas encore activé. Veuillez vérifier votre email.";
            } else {
                if (password_verify($password, $etudiant['mot_de_passe'])) {
                    $_SESSION['user_id'] = $etudiant['id'];
                    $_SESSION['user_email'] = $etudiant['email'];
                    $_SESSION['user_name'] = $etudiant['prenom'] . ' ' . $etudiant['nom'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Mot de passe incorrect.";
                }
            }
        } else {
            $message = "Aucun compte trouvé avec cet email.";
        }
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion Étudiant</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/main.js"></script>
</head>
<body>
<div class="container">
    <h2>Connexion Étudiant</h2>

    <form action="login.php" method="post">
        <label>Email :</label>
        <input type="email" name="email" required><br>

        <label>Mot de Passe :</label>
        <input type="password" name="password" required><br>

        <button type="submit">Se connecter</button>
    </form>

    <p id="message">
        <?php if (!empty($message)) echo htmlspecialchars($message); ?>
    </p>

    <a href="http://localhost:8080/PHP/acceuil.php">⬅️ Retour à l'Accueil</a>
</div>
</body>
</html>