<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $fichier = $_FILES['profile_image'];
    $nom_fichier = basename($fichier['name']);
    $taille_fichier = $fichier['size'];
    $type_fichier = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));

    $stmt = $pdo->prepare("SELECT apogee FROM etudiants WHERE id = ?");
    $stmt->execute([$etudiant_id]);
    $etudiant = $stmt->fetch();
    $apogee = $etudiant['apogee'];

    $dossier_photos = "uploads/photos/$apogee/";
    $chemin_fichier = $dossier_photos . uniqid() . '.' . $type_fichier;

    $types_autorises = ['jpg', 'jpeg', 'png'];
    $taille_max = 2 * 1024 * 1024; // 2 Mo

    if (!in_array($type_fichier, $types_autorises)) {
        $message = "Format de fichier non autorisé (JPG, PNG uniquement).";
    } elseif ($taille_fichier > $taille_max) {
        $message = "Le fichier est trop volumineux (max 2 Mo).";
    } else {
        if (!is_dir($dossier_photos)) {
            mkdir($dossier_photos, 0777, true);
        }

        if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
            $stmt = $pdo->prepare("UPDATE etudiants SET photo_path = ? WHERE id = ?");
            $stmt->execute([$chemin_fichier, $etudiant_id]);
            $message = "Photo de profil mise à jour avec succès.";
        } else {
            $message = "Erreur lors de l'upload de la photo.";
        }
    }
} else {
    $message = "Aucun fichier sélectionné.";
}

header("Location: dashboard.php?message=" . urlencode($message));
exit();