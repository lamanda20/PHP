<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();

if ($etudiant === false) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=session_invalid");
    exit();
}

$stmt = $pdo->prepare("SELECT m.id_module, m.nom_module 
                       FROM inscriptions_modules im 
                       JOIN modules m ON im.id_module = m.id_module 
                       WHERE im.id_etudiant = ?");
$stmt->execute([$etudiant_id]);
$modules = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT j.*, m.nom_module, f.nom_filiere 
                       FROM justificatifs j 
                       JOIN modules m ON j.module_id = m.id_module 
                       JOIN filieres f ON m.id_filiere = f.id_filiere 
                       WHERE j.etudiant_id = ? AND j.statut = 'marque_par_admin'");
$stmt->execute([$etudiant_id]);
$justificatifs_marques = $stmt->fetchAll();

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
    </header>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <section class="card">
        <h3>Informations personnelles</h3>
        <div class="profile-section">
            <?php if ($etudiant['photo_path']): ?>
                <img src="<?php echo htmlspecialchars($etudiant['photo_path']); ?>" alt="Photo de profil" class="profile-image">
            <?php else: ?>
                <p>Aucune photo d'identité téléversée.</p>
            <?php endif; ?>
            <div class="profile-info">
                <p><strong>Email :</strong> <?php echo htmlspecialchars($etudiant['email']); ?></p>
                <p><strong>Apogée :</strong> <?php echo htmlspecialchars($etudiant['apogee']); ?></p>
                <p><strong>Filière :</strong> <?php 
                    $stmt = $pdo->prepare("SELECT nom_filiere FROM filieres WHERE id_filiere = ?");
                    $stmt->execute([$etudiant['id_filiere']]);
                    $filiere = $stmt->fetch();
                    echo htmlspecialchars($filiere['nom_filiere']);
                ?></p>
            </div>
        </div>
        <h4>Mettre à jour la photo de profil</h4>
        <form action="upload_profile_image.php" method="post" enctype="multipart/form-data" class="form-group">
            <label>Sélectionner une image (JPG, PNG, max 2 Mo) :</label>
            <input type="file" name="profile_image" accept=".jpg,.jpeg,.png" required class="form-input">
            <button type="submit" class="btn">Téléverser</button>
        </form>
    </section>

    <section class="card">
        <h3>S'inscrire à un module</h3>
        <form action="inscription_module.php" method="post" class="form-group">
            <label>Module :</label>
            <select name="module_id" required class="form-input">
                <?php
                $stmt = $pdo->prepare("SELECT id_module, nom_module FROM modules WHERE id_filiere = ?");
                $stmt->execute([$etudiant['id_filiere']]);
                while ($module = $stmt->fetch()) {
                    echo "<option value='{$module['id_module']}'>" . htmlspecialchars($module['nom_module']) . "</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn">S'inscrire</button>
        </form>
    </section>

    <h2>Gestion de vos fichiers</h2>
    <section class="card">
        <h3>Ajouter un fichier</h3>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="form-group">
            <label>Sélectionner un fichier :</label>
            <input type="file" name="fichier" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required class="form-input">
            <button type="submit" class="btn">Téléverser</button>
        </form>
    </section>

    <section class="card">
        <h3>Ajouter un justificatif d'absence</h3>
        <?php if (empty($modules)): ?>
            <p class="error-message">Vous devez d'abord vous inscrire à un module avant de pouvoir soumettre un justificatif d'absence.</p>
        <?php else: ?>
            <form action="upload_justificatif.php" method="post" enctype="multipart/form-data" class="form-group">
                <label>Date d'absence :</label>
                <input type="date" name="date_absence" required class="form-input">
                <label>Module :</label>
                <select name="module_id" required class="form-input">
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id_module']; ?>"><?php echo htmlspecialchars($module['nom_module']); ?></option>
                    <?php endforeach; ?>
                </select>
                <label>Fichier justificatif :</label>
                <input type="file" name="justificatif" accept=".pdf,.jpg,.jpeg,.png" required class="form-input">
                <p>Formats acceptés : PDF, JPG, PNG (max 5 Mo).</p>
                <button type="submit" class="btn">Soumettre</button>
            </form>
        <?php endif; ?>
    </section>

    <section class="card">
        <h3>Vos fichiers</h3>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM fichiers WHERE etudiant_id = ?");
        $stmt->execute([$etudiant_id]);
        $fichiers = $stmt->fetchAll();
        if (count($fichiers) > 0) : ?>
            <ul class="file-list">
                <?php foreach ($fichiers as $fichier) : ?>
                    <li>
                        <a href='uploads/<?php echo htmlspecialchars($fichier['nom_fichier']); ?>' target='_blank'>
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

    <section class="card">
        <h3>Vos justificatifs d'absence</h3>
        <?php
        $stmt = $pdo->prepare("SELECT j.*, m.nom_module 
                               FROM justificatifs j 
                               JOIN modules m ON j.module_id = m.id_module 
                               WHERE j.etudiant_id = ?");
        $stmt->execute([$etudiant_id]);
        $justificatifs = $stmt->fetchAll();
        if (count($justificatifs) > 0) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date d'absence</th>
                        <th>Module</th>
                        <th>Fichier</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($justificatifs as $justificatif) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($justificatif['date_absence']); ?></td>
                            <td><?php echo htmlspecialchars($justificatif['nom_module']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($justificatif['fichier_path']); ?>" target="_blank">
                                    <?php echo htmlspecialchars(basename($justificatif['fichier_path'])); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($justificatif['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Aucun justificatif soumis.</p>
        <?php endif; ?>
    </section>

    <section class="card">
        <h3>Historique des absences marquées par l'administrateur</h3>
        <?php if (count($justificatifs_marques) > 0) : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date d'absence</th>
                        <th>Module</th>
                        <th>Filière</th>
                        <th>Fichier</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($justificatifs_marques as $justificatif) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($justificatif['date_absence']); ?></td>
                            <td><?php echo htmlspecialchars($justificatif['nom_module']); ?></td>
                            <td><?php echo htmlspecialchars($justificatif['nom_filiere']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($justificatif['fichier_path']); ?>" target="_blank">
                                    <?php echo htmlspecialchars(basename($justificatif['fichier_path'])); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($justificatif['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>Aucun historique d'absence marqué par l'administrateur.</p>
        <?php endif; ?>
    </section>

    <div class="footer">
        <a href="logout.php" class="btn">🚪 Se déconnecter</a>
        <a href="http://localhost:8080/PHP/acceuil.php" class="btn">⬅ Retour à l'Accueil</a>
    </div>
</div>
</body>
</html>