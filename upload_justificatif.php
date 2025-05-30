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
    $date_absence = $_POST['date_absence'];
    $module_id = intval($_POST['module_id']);
    $fichier = $_FILES['justificatif'];
    $nom_fichier = basename($fichier['name']);
    $taille_fichier = $fichier['size'];
    $type_fichier = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));

    $stmt = $pdo->prepare("SELECT apogee FROM etudiants WHERE id = ?");
    $stmt->execute([$etudiant_id]);
    $etudiant = $stmt->fetch();
    $apogee = $etudiant['apogee'];

    $dossier_justificatif = "justificatifs/$apogee/";
    $chemin_fichier = $dossier_justificatif . uniqid() . '.' . $type_fichier;

    $types_autorises = ['pdf', 'jpg', 'jpeg', 'png'];
    $taille_max = 5 * 1024 * 1024;

    if (!in_array($type_fichier, $types_autorises)) {
        $message = "Format de fichier non autorisé (PDF, JPG, PNG uniquement).";
    } elseif ($taille_fichier > $taille_max) {
        $message = "Le fichier est trop volumineux (max 5 Mo).";
    } elseif (empty($date_absence) || !strtotime($date_absence)) {
        $message = "Veuillez entrer une date d'absence valide.";
    } elseif ($module_id <= 0) {
        $message = "Veuillez sélectionner un module valide.";
    } else {
        if (!is_dir($dossier_justificatif)) {
            mkdir($dossier_justificatif, 0777, true);
        }

        if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
            $stmt = $pdo->prepare("INSERT INTO justificatifs (etudiant_id, module_id, date_absence, fichier_path, statut) 
                                   VALUES (?, ?, ?, ?, 'en attente')");
            $stmt->execute([$etudiant_id, $module_id, $date_absence, $chemin_fichier]);
            $message = "Justificatif soumis avec succès.";
        } else {
            $message = "Erreur lors de l'upload du justificatif.";
        }
    }
} else {
    $message = "Aucun fichier sélectionné.";
}

header("Location: dashboard.php?message=" . urlencode($message));
exit();