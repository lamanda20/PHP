<?php
// verify_code.php - Vérification du code d'activation des étudiants

// Démarrer la session
session_start();
require_once 'db.php';

// Vérification si la requête est POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $code = htmlspecialchars(trim($_POST['code']));
    $message = "";

    try {
        // Vérifier si l'email et le code existent dans la base de données
        $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE email = ? AND code_activation = ?");
        $stmt->execute([$email, $code]);
        $etudiant = $stmt->fetch();

        if ($etudiant) {
            // Vérifier si le compte est déjà activé
            if ($etudiant['est_active'] == 1) {
                $message = "Votre compte est déjà activé. Vous pouvez vous connecter.";
            } else {
                // Activer le compte
                $stmt = $pdo->prepare("UPDATE etudiants SET est_active = 1 WHERE email = ?");
                $stmt->execute([$email]);
                $message = "Activation réussie. Vous pouvez maintenant vous connecter.";
            }
        } else {
            $message = "Code d'activation incorrect ou compte inexistant.";
        }
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
    }

    // Retour du message en réponse JSON pour l'AJAX
    echo $message;
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Activation de Compte</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/main.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
    <h1>Activation de votre Compte</h1>

    <div class="card">
        <!-- Formulaire de vérification de code -->
        <form onsubmit="event.preventDefault(); verifyCode();">
            <label for="email">Email :</label>
            <input type="email" id="email" placeholder="Votre email" required>

            <label for="code">Code d'activation :</label>
            <input type="text" id="code" placeholder="Code d'activation" required>

            <button type="submit" class="btn">Vérifier</button>
        </form>

        <!-- Affichage du message de retour -->
        <div id="message" class="mt-3"></div>
    </div>

    <!-- Bouton retour à l'accueil -->
    <div class="mt-4 text-center">
        <a href="http://localhost:63342/GHADI_Taha/acceuil.php" class="btn-secondary">⬅ Retour à l'Accueil</a>
    </div>
</div>

<script>
    function verifyCode() {
        const email = document.getElementById("email").value;
        const code = document.getElementById("code").value;
        const messageElement = document.getElementById("message");

        // Afficher un indicateur de chargement
        messageElement.innerHTML = '<div class="loader"></div>';

        // Vérification par AJAX
        fetch('verify_code.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `email=${encodeURIComponent(email)}&code=${encodeURIComponent(code)}`
        })
            .then(response => response.text())
            .then(data => {
                // Suppression du loader
                messageElement.innerHTML = '';

                // Création d'un élément de message avec la classe appropriée
                const messageClass = data.includes("Activation réussie") || data.includes("déjà activé")
                    ? "message message-success"
                    : "message message-error";

                messageElement.innerHTML = `<div class="${messageClass}">${data}</div>`;

                // Redirection si activation réussie
                if (data.includes("Activation réussie")) {
                    setTimeout(() => {
                        window.location.href = "http://localhost:63342/GHADI_Taha/login.php";
                    }, 2000);
                }
            })
            .catch(error => {
                messageElement.innerHTML = `<div class="message message-error">Erreur de connexion: ${error}</div>`;
            });
    }
</script>
</body>
</html>