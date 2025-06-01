<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['justificatif'])) {
    $date_absence = htmlspecialchars(trim($_POST['date_absence']));
    $module_id = intval($_POST['module_id']);
    $fichier = $_FILES['justificatif'];
    $nom_fichier = basename($fichier['name']);
    $taille_fichier = $fichier['size'];
    $type_fichier = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));
    $dossier_televersement = "uploads/";
    $chemin_fichier = $dossier_televersement . uniqid() . '.' . $type_fichier;

    // Vérifier le type de fichier autorisé
    $types_autorises = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($type_fichier, $types_autorises)) {
        $message = "Format de fichier non autorisé. (PDF, JPG, PNG uniquement)";
    } elseif ($taille_fichier > 5 * 1024 * 1024) { // Limite de 5 Mo
        $message = "Le fichier est trop volumineux (limite de 5 Mo).";
    } else {
        // Vérifier si le dossier d'upload existe, sinon le créer
        if (!is_dir($dossier_televersement)) {
            mkdir($dossier_televersement, 0777, true);
        }

        // Déplacer le fichier vers le dossier d'upload
        if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
            // Enregistrer le justificatif dans la base de données
            $stmt = $pdo->prepare("INSERT INTO justificatifs (etudiant_id, module_id, date_absence, fichier_path, statut) 
                                   VALUES (?, ?, ?, ?, 'en_attente')");
            $stmt->execute([$etudiant_id, $module_id, $date_absence, $chemin_fichier]);
            $message = "Justificatif soumis avec succès. En attente de validation.";
        } else {
            $message = "Erreur lors du téléversement du fichier.";
        }
    }
} else {
    $message = "Aucun fichier sélectionné.";
}

header("Location: dashboard.php?message=" . urlencode($message));
exit();
?>