<?php
session_start();
require_once 'db.php';
require_once 'send_email.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $apogee = htmlspecialchars(trim($_POST['apogee']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $code_activation = rand(10000000, 99999999);
    $id_filiere = intval($_POST['id_filiere']);

    if ($password !== $confirm_password) {
        $message_type = "error";
        $message = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $message_type = "error";
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("SELECT * FROM etudiants WHERE email = ? OR apogee = ?");
            $stmt->execute([$email, $apogee]);
            if ($stmt->rowCount() > 0) {
                $message_type = "error";
                $message = "Email ou Apogée déjà utilisé.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO etudiants (nom, prenom, apogee, email, mot_de_passe, code_activation, id_filiere) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $apogee, $email, $hashed_password, $code_activation, $id_filiere]);

                if (sendActivationEmail($email, $prenom, $code_activation)) {
                    $message_type = "success";
                    $message = "Inscription réussie ! Un email d'activation a été envoyé à votre adresse.";
                } else {
                    $message_type = "warning";
                    $message = "Inscription réussie, mais l'email d'activation n'a pas pu être envoyé.";
                }
            }
        } catch (Exception $e) {
            $message_type = "error";
            $message = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription Étudiant</title>
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
    <h1>Inscription Étudiant</h1>

    <?php if (isset($message) && isset($message_type) && $message_type === "success"): ?>
        <div class="message message-success">
            <?php echo htmlspecialchars($message); ?>
        </div>

        <div class="card mt-3">
            <h3>Prochaines étapes</h3>
            <p>1. Vérifiez votre boîte de réception pour trouver l'email d'activation</p>
            <p>2. Cliquez sur le lien dans l'email ou utilisez le code fourni</p>
            <p>3. Connectez-vous avec vos identifiants après activation</p>

            <div class="flex gap-4 mt-3">
                <a href="verify_code.php" class="btn">Activer mon compte</a>
                <a href="login.php" class="btn-secondary">Se connecter</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <form action="register.php" method="post">
                <div class="flex gap-4">
                    <div class="w-full">
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" required>
                    </div>
                    <div class="w-full">
                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" required>
                    </div>
                </div>
                <label for="apogee">Apogée :</label>
                <input type="text" id="apogee" name="apogee" required>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
                <label for="password">Mot de Passe :</label>
                <input type="password" id="password" name="password" required>
                <p class="text-muted mb-2">Le mot de passe doit contenir au moins 8 caractères.</p>
                <label for="confirm_password">Confirmer Mot de Passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <label for="id_filiere">Filière :</label>
                <select id="id_filiere" name="id_filiere" required>
                    <?php
                    $stmt = $pdo->query("SELECT id_filiere, nom_filiere FROM filieres");
                    while ($filiere = $stmt->fetch()) {
                        echo "<option value='{$filiere['id_filiere']}'>" . htmlspecialchars($filiere['nom_filiere']) . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn">S'inscrire</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (isset($message) && isset($message_type) && ($message_type === "error" || $message_type === "warning")): ?>
        <div class="message <?php echo $message_type === 'error' ? 'message-error' : 'message-success'; ?> mt-3">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mt-4">
        <p class="text-muted">Déjà inscrit ?</p>
        <div class="flex gap-4">
            <a href="login.php" class="btn-secondary">Se connecter</a>
            <a href="http://localhost:8080/PHP/acceuil.php" class="btn-secondary">⬅ Retour à l'Accueil</a>
        </div>
    </div>
</div>
</body>
</html>