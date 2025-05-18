<?php
// login.php - Connexion des Étudiants

// Démarrer la session
session_start();
require_once __DIR__ . '/db.php'; // Correction du chemin relatif sécurisé

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));

    try {
        // Vérifier si l'utilisateur existe
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE email = ?");
        $stmt->execute([$email]);
        $etudiant = $stmt->fetch();

        if ($etudiant) {
            // Vérifier si le compte est activé
            if ($etudiant['est_active'] == 0) { // Correction: 'is_active' -> 'est_active'
                $message = "Votre compte n'est pas encore activé. Veuillez vérifier votre email.";
            } else {
                // Vérifier le mot de passe (Correction: 'password' -> 'mot_de_passe')
                if (password_verify($password, $etudiant['mot_de_passe'])) {
                    // Connexion réussie
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
    <link rel="stylesheet" href="/css/style.css"> <!-- Correction du chemin CSS -->
    <script src="/js/main.js"></script> <!-- Correction du chemin JavaScript -->
</head>
<body>
<div class="container">
    <h2>Connexion Étudiant</h2>

    <!-- Formulaire de connexion -->
    <form action="login.php" method="post">
        <label>Email :</label>
        <input type="email" name="email" required><br>

        <label>Mot de Passe :</label>
        <input type="password" name="password" required><br>

        <button type="submit">Se connecter</button>
    </form>

    <!-- Affichage du message de retour -->
    <p id="message">
        <?php if (!empty($message)) echo htmlspecialchars($message); ?>
    </p>

    <a href="http://localhost:63342/PHP/acceuil.php">⬅️ Retour à l'Accueil</a>
</div>
</body>
</html>
