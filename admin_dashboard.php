<?php
// admin_dashboard.php - Tableau de Bord Administrateur

// Démarrer la session
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est déjà connecté en tant qu'administrateur
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    // Afficher le tableau de bord si l'administrateur est connecté
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Tableau de Bord Administrateur</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <div class="container">
        <h1>Bienvenue, Administrateur</h1>

        <h2>Gestion des Étudiants</h2>
        <div class="table-responsive">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Date d'Inscription</th>
                    <th>Fichier (Téléchargement)</th>
                </tr>
                <?php
                // Récupération de tous les étudiants et leurs fichiers
                $stmt = $pdo->query("SELECT e.id, e.nom, e.prenom, e.email, e.date_inscription, f.nom_fichier 
                                    FROM etudiants e 
                                    LEFT JOIN fichiers f ON e.id = f.etudiant_id 
                                    ORDER BY e.nom, e.prenom");

                while ($etudiant = $stmt->fetch()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($etudiant['id']) . "</td>
                            <td>" . htmlspecialchars($etudiant['prenom']) . " " . htmlspecialchars($etudiant['nom']) . "</td>
                            <td>" . htmlspecialchars($etudiant['email']) . "</td>
                            <td>" . htmlspecialchars($etudiant['date_inscription']) . "</td>
                            <td>";

                    if ($etudiant['nom_fichier']) {
                        echo "<a href='uploads/" . htmlspecialchars($etudiant['nom_fichier']) . "' target='_blank'>Télécharger</a>";
                    } else {
                        echo "Aucun fichier";
                    }

                    echo "</td>
                        </tr>";
                }
                ?>
            </table>
        </div>

        <h3>Options :</h3>
        <a href="logout.php" class="btn">🚪 Se déconnecter</a>
    </div>
    </body>
    </html>
    <?php
} else {
    // Formulaire de Connexion Administrateur

    // Traitement de la connexion administrateur
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = htmlspecialchars(trim($_POST['email']));
        $password = htmlspecialchars(trim($_POST['password']));
        $message = "";

        try {
            // Vérifier si l'administrateur existe
            $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin) {
                // Vérifier le mot de passe haché
                if (password_verify($password, $admin['password'])) {
                    // Connexion réussie
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['user_email'] = $admin['email'];
                    $_SESSION['role'] = 'admin';
                    header("Location: /GHADI_Taha/admin_dashboard.php");
                    exit();
                } else {
                    $message = "Mot de passe incorrect.";
                }
            } else {
                $message = "Adresse email ou mot de passe incorrect.";
            }
        } catch (Exception $e) {
            $message = "Erreur de connexion. Veuillez réessayer.";
        }
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Connexion Administrateur</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <div class="container">
        <h1>Connexion Administrateur</h1>

        <!-- Formulaire de connexion -->
        <form action="admin_dashboard.php" method="post">
            <label>Email :</label>
            <input type="email" name="email" required>

            <label>Mot de Passe :</label>
            <input type="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>

        <!-- Affichage du message de retour -->
        <?php if (isset($message) && !empty($message)): ?>
            <p class="message message-error">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <!-- Bouton retour à l'accueil déplacé en bas -->
        <div class="mt-4 text-center">
            <a href="http://localhost:63342/GHADI_Taha/acceuil.php" class="btn-secondary">⬅ Retour à l'Accueil</a>
        </div>
    </div>
    </body>
    </html>
    <?php
}
?>