<?php
// upload_file.php - Téléversement sécurisé de fichiers par les étudiants

// Démarrer la session
session_start();
require_once 'db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération de l'ID de l'étudiant connecté
$etudiant_id = $_SESSION['user_id'];
$message = "";

// Traitement du téléversement de fichier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fichier'])) {
    $fichier = $_FILES['fichier'];
    $nom_fichier = basename($fichier['name']);
    $taille_fichier = $fichier['size'];
    $type_fichier = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));
    $dossier_televersement = "uploads/";
    $chemin_fichier = $dossier_televersement . uniqid() . '.' . $type_fichier;

    // Vérifier le type de fichier autorisé
    $types_autorises = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    if (!in_array($type_fichier, $types_autorises)) {
        $message = "Format de fichier non autorisé. (PDF, JPG, PNG, DOC, DOCX uniquement)";
    } elseif ($taille_fichier > 5 * 1024 * 1024) { // Limite de 5 Mo
        $message = "Le fichier est trop volumineux (limite de 5 Mo).";
    } else {
        // Vérifier si le dossier d'upload existe, sinon le créer
        if (!is_dir($dossier_televersement)) {
            mkdir($dossier_televersement, 0777, true);
        }

        // Déplacer le fichier vers le dossier d'upload
        if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
            // Enregistrer le fichier dans la base de données
            $stmt = $pdo->prepare("INSERT INTO fichiers (etudiant_id, nom_fichier, date_upload) 
                                   VALUES (?, ?, NOW())");
            $stmt->execute([$etudiant_id, basename($chemin_fichier)]);
            $message = "Fichier téléversé avec succès.";
        } else {
            $message = "Erreur lors du téléversement du fichier.";
        }
    }
} else {
    $message = "Aucun fichier sélectionné.";
}

// Rediriger vers le tableau de bord avec un message
header("Location: dashboard.php?message=" . urlencode($message));
exit();
