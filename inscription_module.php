<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$etudiant_id = $_SESSION['user_id'];
$module_id = intval($_POST['module_id']);
$message = "";

try {
    $stmt = $pdo->prepare("SELECT * FROM inscriptions_modules WHERE id_etudiant = ? AND id_module = ?");
    $stmt->execute([$etudiant_id, $module_id]);
    if ($stmt->rowCount() > 0) {
        $message = "Vous êtes déjà inscrit à ce module.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO inscriptions_modules (id_etudiant, id_module) VALUES (?, ?)");
        $stmt->execute([$etudiant_id, $module_id]);
        $message = "Inscription au module réussie.";
    }
} catch (Exception $e) {
    $message = "Erreur : " . $e->getMessage();
}

header("Location: dashboard.php?message=" . urlencode($message));
exit();