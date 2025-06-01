<?php
// create_password.php - Création de mot de passe après activation

// Démarrer la session
session_start();
require_once 'db.php';

// Vérification de l'activation du compte
if (!isset($_SESSION['email_activated'])) {
    header("Location: verify_code.php");
    exit();
}

$email = $_SESSION['email_activated'];
$message = "";

// Traitement de la création de mot de passe
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Mettre à jour le mot de passe dans la base de données
        $stmt = $pdo->prepare("UPDATE etudiants SET password = ? WHERE email = ?");
        $stmt->execute([$hashed_password, $email]);

        // Supprimer l'email de session après la création du mot de passe
        unset($_SESSION['email_activated']);

        // Redirection vers la page de connexion
        header("Location: login.php?message=Mot de passe créé avec succès. Vous pouvez maintenant vous connecter.");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer votre Mot de Passe</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/main.js"></script> <!-- Inclusion de main.js -->
</head>
<body>
<h2>Créer votre Mot de Passe</h2>

<!-- Formulaire de création de mot de passe -->
<form action="create_password.php" method="post">
    <label>Nouveau Mot de Passe :</label>
    <input type="password" name="password" required><br>

    <label>Confirmer Mot de Passe :</label>
    <input type="password" name="confirm_password" required><br>

    <button type="submit">Créer le Mot de Passe</button>
</form>

<!-- Affichage du message de retour -->
<p id="message">
    <?php if (!empty($message)) echo htmlspecialchars($message); ?>
</p>

<a href="http://localhost:8080/PHP/acceuil.php">⬅️ Retour à l'Accueil</a>
</body>
</html>
