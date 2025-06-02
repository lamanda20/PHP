<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['justificatif']) && isset($_POST['justificatif_id'])) {
    $justificatif_id = intval($_POST['justificatif_id']);
    $fichier = $_FILES['justificatif'];
    $nom_fichier = basename($fichier['name']);
    $taille_fichier = $fichier['size'];
    $type_fichier = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));
    $dossier_televersement = "Uploads/";
    $chemin_fichier = $dossier_televersement . uniqid() . '.' . $type_fichier;

    // Verify the justificatif belongs to the student and is admin-marked
    $stmt = $pdo->prepare("SELECT * FROM justificatifs WHERE id = ? AND etudiant_id = ? AND marque_par_admin = TRUE AND fichier_path IS NULL");
    $stmt->execute([$justificatif_id, $etudiant_id]);
    if ($stmt->rowCount() == 0) {
        $message = "Justificatif invalide ou déjà soumis.";
    } else {
        $types_autorises = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($type_fichier, $types_autorises)) {
            $message = "Format de fichier non autorisé. (PDF, JPG, PNG uniquement)";
        } elseif ($taille_fichier > 5 * 1024 * 1024) {
            $message = "Le fichier est trop volumineux (limite de 5 Mo).";
        } else {
            if (!is_dir($dossier_televersement)) {
                mkdir($dossier_televersement, 0777, true);
            }

            if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
                $stmt = $pdo->prepare("UPDATE justificatifs SET fichier_path = ?, statut = 'en_attente' WHERE id = ?");
                $stmt->execute([$chemin_fichier, $justificatif_id]);
                $message = "Justificatif soumis avec succès. En attente de validation.";
            } else {
                $message = "Erreur lors du téléversement du fichier.";
            }
        }
    }
} else {
    $message = "Aucun fichier sélectionné ou justificatif invalide.";
}

header("Location: dashboard.php?message=" . urlencode($message));
exit();
?>