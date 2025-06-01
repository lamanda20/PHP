<?php
global $stmt;
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['justificatif'])) {
    $module_id = intval($_POST['module_id']);
    $date_absence = $_POST['date_absence'] ?? date('Y-m-d'); // valeur par défaut

    $fichier = $_FILES['justificatif'];
    $nom_fichier = basename($fichier['name']);
    $taille_fichier = $fichier['size'];
    $type_fichier = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));

    $types_autorises = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!in_array($type_fichier, $types_autorises)) {
        $message = "Type de fichier non autorisé.";
    } else {
        $chemin_fichier = 'uploads/' . uniqid() . '_' . $nom_fichier;

        if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
            $stmt = $pdo->prepare("INSERT INTO justificatifs (etudiant_id, module_id, date_absence, fichier_path, statut) 
                                   VALUES (?, ?, ?, ?, 'en attente')");
            $stmt->execute([$etudiant_id, $module_id, $date_absence, $chemin_fichier]);
            $message = "Justificatif envoyé avec succès.";
        } else {
            $message = "Erreur lors du téléchargement du fichier.";
        }
    }
} else {
    $message = "Aucun fichier sélectionné.";
}

header("Location: dashboard.php?message=" . urlencode($message));
exit();
