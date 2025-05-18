<?php
// dashboard.php - Tableau de Bord des Étudiants

// Démarrer la session
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération des informations de l'étudiant connecté
$etudiant_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tableau de Bord Étudiant</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/main.js"></script> <!-- Inclusion de main.js -->
</head>
<body>
<div class="container">
    <h1>Bienvenue, <?php echo htmlspecialchars($etudiant['prenom']) . ' ' . htmlspecialchars($etudiant['nom']); ?></h1>

    <div class="card mb-3">
        <div class="flex justify-between items-center mb-2">
            <h3 class="mb-0">Informations personnelles</h3>
        </div>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($etudiant['email']); ?></p>
        <p class="mb-0"><strong>Apogée :</strong> <?php echo htmlspecialchars($etudiant['apogee']); ?></p>
    </div>

    <h2>Gestion de vos fichiers</h2>

    <!-- Formulaire de téléversement de fichier -->
    <div class="card mb-3">
        <h3>Ajouter un fichier</h3>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label>Sélectionner un fichier :</label>
            <input type="file" name="fichier" required>
            <button type="submit" class="btn">Téléverser</button>
        </form>
    </div>

    <div class="card">
        <h3>Vos fichiers</h3>
        <?php
        // Récupérer les fichiers de l'étudiant
        $stmt = $pdo->prepare("SELECT * FROM fichiers WHERE etudiant_id = ?");
        $stmt->execute([$etudiant_id]);
        $fichiers = $stmt->fetchAll();

        if (count($fichiers) > 0) : ?>
            <ul>
                <?php foreach ($fichiers as $fichier) : ?>
                    <li class="mb-1">
                        <a href='uploads/<?php echo htmlspecialchars($fichier['nom_fichier']); ?>' target='_blank'>
                            <?php echo htmlspecialchars($fichier['nom_fichier']); ?>
                        </a>
                        <span class="text-muted"> - <?php echo htmlspecialchars($fichier['date_upload']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p class="text-muted">Vous n'avez pas encore téléversé de fichiers.</p>
        <?php endif; ?>
    </div>

    <div class="flex justify-between items-center mt-4">
        <a href="logout.php" class="btn">🚪 Se déconnecter</a>
        <a href="http://localhost:63342/GHADI_Taha/acceuil.php" class="btn-secondary">⬅ Retour à l'Accueil</a>
    </div>
</div>
</body>
</html>