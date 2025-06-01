<?php
// admin_dashboard.php - Tableau de Bord Administrateur

// Démarrer la session
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est déjà connecté en tant qu'administrateur
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
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

        <!-- Liste des Présences (QR Scan) -->
        <div class="table-responsive">
            <h2>Présences Enregistrées Aujourd'hui</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>ID Étudiant</th>
                    <th>Heure d'arrivée</th>
                </tr>
                <?php
                $stmt = $pdo->query("SELECT id, student_id, time_in 
                                    FROM table_attendance 
                                    WHERE DATE(time_in) = CURDATE() 
                                    ORDER BY time_in");
                while ($presence = $stmt->fetch()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($presence['id'] ?? '') . "</td>
                            <td>" . htmlspecialchars($presence['student_id'] ?? '') . "</td>
                            <td>" . htmlspecialchars($presence['time_in'] ?? '') . "</td>
                        </tr>";
                }
                if ($stmt->rowCount() == 0) {
                    echo "<tr><td colspan='3'>Aucune présence enregistrée aujourd'hui</td></tr>";
                }
                ?>
            </table>
        </div>

        <!-- Gestion des Étudiants par Filière et Module -->
        <h2>Gestion des Étudiants</h2>
        <div class="table-responsive">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom Complet</th>
                    <th>Email</th>
                    <th>Date d'Inscription</th>
                    <th>Filière</th>
                    <th>Module</th>
                    <th>Fichier (Téléchargement)</th>
                    <th>Actions</th>
                </tr>
                <?php
                $stmt = $pdo->query("SELECT e.id, e.nom, e.prenom, e.email, e.date_inscription, f.nom_filiere, m.nom_module, fi.nom_fichier 
                                    FROM etudiants e 
                                    LEFT JOIN filieres f ON e.id_filiere = f.id_filiere 
                                    LEFT JOIN inscriptions_modules im ON e.id = im.id_etudiant 
                                    LEFT JOIN modules m ON im.id_module = m.id_module 
                                    LEFT JOIN fichiers fi ON e.id = fi.etudiant_id 
                                    ORDER BY e.nom, e.prenom");
                while ($etudiant = $stmt->fetch()) {
                    $stmt_modules = $pdo->prepare("SELECT id_module, nom_module FROM modules WHERE id_module IN (SELECT id_module FROM inscriptions_modules WHERE id_etudiant = ?)");
                    $stmt_modules->execute([$etudiant['id']]);
                    $modules = $stmt_modules->fetchAll();
                    echo "<tr>
                            <td>" . htmlspecialchars($etudiant['id'] ?? '') . "</td>
                            <td>" . htmlspecialchars($etudiant['prenom'] ?? '') . " " . htmlspecialchars($etudiant['nom'] ?? '') . "</td>
                            <td>" . htmlspecialchars($etudiant['email'] ?? '') . "</td>
                            <td>" . htmlspecialchars($etudiant['date_inscription'] ?? '') . "</td>
                            <td>" . htmlspecialchars($etudiant['nom_filiere'] ?? '') . "</td>
                            <td>" . htmlspecialchars($etudiant['nom_module'] ?? '') . "</td>
                            <td>";
                    if ($etudiant['nom_fichier']) {
                        echo "<a href='uploads/" . htmlspecialchars($etudiant['nom_fichier']) . "' target='_blank'>Télécharger</a>";
                    } else {
                        echo "Aucun fichier";
                    }
                    echo "</td>
                            <td>
                                <form action='mark_absence.php' method='post' style='display:inline;'>
                                    <input type='hidden' name='etudiant_id' value='" . htmlspecialchars($etudiant['id'] ?? '') . "'>
                                    <select name='module_id' required class='form-input' style='margin-right: 10px;'>";
                    if (!empty($modules)) {
                        foreach ($modules as $module) {
                            echo "<option value='" . htmlspecialchars($module['id_module'] ?? '') . "'>" . htmlspecialchars($module['nom_module'] ?? '') . "</option>";
                        }
                    } else {
                        echo "<option value='1'>Aucun module</option>";
                    }
                    echo "</select>
                                    <button type='submit' class='btn'>Marquer Absent</button>
                                </form>
                            </td>
                        </tr>";
                }
                ?>
            </table>
        </div>

        <!-- Affichage des Justificatifs -->
        <h2>Justificatifs Soumis par les Étudiants</h2>
        <div class="table-responsive">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom Complet</th>
                    <th>Date d'Absence</th>
                    <th>Module</th>
                    <th>Fichier</th>
                    <th>Statut</th>
                </tr>
                <?php
                $stmt = $pdo->query("SELECT j.id, e.prenom, e.nom, j.date_absence, m.nom_module, j.fichier_path, j.statut 
                                    FROM justificatifs j 
                                    JOIN etudiants e ON j.etudiant_id = e.id 
                                    JOIN modules m ON j.module_id = m.id_module 
                                    WHERE j.marque_par_admin = FALSE 
                                    ORDER BY j.date_absence");
                while ($justificatif = $stmt->fetch()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($justificatif['id'] ?? '') . "</td>
                            <td>" . htmlspecialchars($justificatif['prenom'] ?? '') . " " . htmlspecialchars($justificatif['nom'] ?? '') . "</td>
                            <td>" . htmlspecialchars($justificatif['date_absence'] ?? '') . "</td>
                            <td>" . htmlspecialchars($justificatif['nom_module'] ?? '') . "</td>
                            <td>";
                    if ($justificatif['fichier_path']) {
                        echo "<a href='" . htmlspecialchars($justificatif['fichier_path']) . "' target='_blank'>Télécharger</a>";
                    } else {
                        echo "Aucun fichier";
                    }
                    echo "</td>
                            <td>" . htmlspecialchars($justificatif['statut'] ?? '') . "</td>
                        </tr>";
                }
                if ($stmt->rowCount() == 0) {
                    echo "<tr><td colspan='6'>Aucun justificatif soumis</td></tr>";
                }
                ?>
            </table>
        </div>

        <h3>Options :</h3>
        <a href="logout.php" class="btn">🚪 Se déconnecter</a>
        <a href="admin_pdf.php" class="btn">Générer Rapport PDF</a>
    </div>
    </body>
    </html>
    <?php
} else {
    // Formulaire de Connexion Administrateur
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = htmlspecialchars(trim($_POST['email']));
        $password = htmlspecialchars(trim($_POST['password']));
        $message = "";

        try {
            $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin) {
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['user_id'] = $admin['id'];
                    $_SESSION['user_email'] = $admin['email'];
                    $_SESSION['role'] = 'admin';
                    header("Location: /PHP/admin_dashboard.php");
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
        <form action="admin_dashboard.php" method="post">
            <label>Email :</label>
            <input type="email" name="email" required>
            <label>Mot de Passe :</label>
            <input type="password" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
        <?php if (isset($message) && !empty($message)): ?>
            <p class="message message-error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div class="mt-4 text-center">
            <a href="http://localhost:8080/PHP/acceuil.php" class="btn-secondary">⬅ Retour à l'Accueil</a>
        </div>
    </div>
    </body>
    </html>
    <?php
}
?>