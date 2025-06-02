<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];

// Fetch student information
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();

if ($etudiant === false) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=session_invalid");
    exit();
}

// Fetch modules for the student
$stmt = $pdo->prepare("SELECT m.id_module, m.nom_module 
                       FROM inscriptions_modules im 
                       JOIN modules m ON im.id_module = m.id_module 
                       WHERE im.id_etudiant = ?");
$stmt->execute([$etudiant_id]);
$modules = $stmt->fetchAll();

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tableau de Bord Étudiant</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="/js/main.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
    <header class="header">
        <h1>Bienvenue, <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?></h1>
        <?php if ($etudiant['photo_path']): ?>
            <img src="<?php echo htmlspecialchars($etudiant['photo_path']); ?>" alt="Photo de profil" class="profile-image" style="max-width: 150px; border-radius: 50%;">
        <?php else: ?>
            <p>Aucune photo de profil disponible.</p>
        <?php endif; ?>
    </header>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <section class="card">
        <h3>Informations personnelles</h3>
        <div class="profile-section">
            <div class="profile-info">
                <p><strong>Email :</strong> <?php echo htmlspecialchars($etudiant['email'] ?? 'Non défini'); ?></p>
                <p><strong>Apogée :</strong> <?php echo htmlspecialchars($etudiant['apogee'] ?? 'Non défini'); ?></p>
                <p><strong>Filière :</strong> <?php 
                    $stmt = $pdo->prepare("SELECT nom_filiere FROM filieres WHERE id_filiere = ?");
                    $stmt->execute([$etudiant['id_filiere']]);
                    $filiere = $stmt->fetch();
                    echo htmlspecialchars($filiere['nom_filiere'] ?? 'Non défini');
                ?></p>
            </div>
        </div>
    </section>

    <section class="card">
        <h3>Ajouter un justificatif pour absence marquée</h3>
        <?php
        $stmt = $pdo->prepare("SELECT j.id, j.date_absence, m.nom_module, f.nom_filiere 
                              FROM justificatifs j 
                              JOIN modules m ON j.module_id = m.id_module 
                              JOIN filieres f ON m.id_filiere = f.id_filiere 
                              WHERE j.etudiant_id = ? AND j.marque_par_admin = TRUE AND j.fichier_path IS NULL 
                              ORDER BY j.date_absence DESC");
        $stmt->execute([$etudiant_id]);
        $absences_admin = $stmt->fetchAll();
        if (count($absences_admin) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date d'Absence</th>
                        <th>Module</th>
                        <th>Filière</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absences_admin as $absence): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($absence['date_absence']); ?></td>
                            <td><?php echo htmlspecialchars($absence['nom_module']); ?></td>
                            <td><?php echo htmlspecialchars($absence['nom_filiere']); ?></td>
                            <td>
                                <form action="upload_justificatif.php" method="post" enctype="multipart/form-data" class="form-group">
                                    <input type="hidden" name="justificatif_id" value="<?php echo $absence['id']; ?>">
                                    <input type="file" name="justificatif" accept=".pdf,.jpg,.jpeg,.png" required class="form-input">
                                    <button type="submit" class="btn">Soumettre</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune absence marquée par l'administrateur sans justificatif.</p>
        <?php endif; ?>
    </section>

    <section class="card">
        <h3>Historique des absences marquées par l'administrateur</h3>
        <?php
        $stmt = $pdo->prepare("SELECT j.date_absence, m.nom_module, f.nom_filiere, j.fichier_path 
                              FROM justificatifs j 
                              JOIN modules m ON j.module_id = m.id_module 
                              JOIN filieres f ON m.id_filiere = f.id_filiere 
                              WHERE j.etudiant_id = ? AND j.marque_par_admin = TRUE 
                              ORDER BY j.date_absence DESC");
        $stmt->execute([$etudiant_id]);
        $absences_admin = $stmt->fetchAll();
        if (count($absences_admin) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date d'Absence</th>
                        <th>Module</th>
                        <th>Filière</th>
                        <th>Justificatif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($absences_admin as $absence): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($absence['date_absence']); ?></td>
                            <td><?php echo htmlspecialchars($absence['nom_module']); ?></td>
                            <td><?php echo htmlspecialchars($absence['nom_filiere']); ?></td>
                            <td>
                                <?php if ($absence['fichier_path']): ?>
                                    <a href="<?php echo htmlspecialchars($absence['fichier_path']); ?>" target="_blank">Télécharger</a>
                                <?php else: ?>
                                    Aucun fichier
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune absence marquée par l'administrateur.</p>
        <?php endif; ?>
    </section>

    <section class="card">
        <h3>Gestion de vos fichiers</h3>
        <h4>Ajouter un fichier</h4>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="form-group">
            <label>Sélectionner un fichier :</label>
            <input type="file" name="fichier" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required class="form-input">
            <button type="submit" class="btn">Téléverser</button>
        </form>

        <h4>Vos fichiers</h4>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM fichiers WHERE etudiant_id = ?");
        $stmt->execute([$etudiant_id]);
        $fichiers = $stmt->fetchAll();
        if (count($fichiers) > 0) : ?>
            <ul class="file-list">
                <?php foreach ($fichiers as $fichier) : ?>
                    <li>
                        <a href='Uploads/<?php echo htmlspecialchars($fichier['nom_fichier']); ?>' target='_blank'>
                            <?php echo htmlspecialchars($fichier['nom_fichier']); ?>
                        </a>
                        <span> - <?php echo htmlspecialchars($fichier['date_upload']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>Vous n'avez pas encore téléversé de fichiers.</p>
        <?php endif; ?>
    </section>

    <div class="footer">
        <a href="logout.php" class="btn">🚪 Se déconnecter</a>
        <a href="http://localhost:63342/PHP/acceuil.php" class="btn">⬅ Retour à l'Accueil</a>
    </div>
</div>
</body>
</html>