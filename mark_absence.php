<?php
// mark_absence.php - Marquer un étudiant comme absent
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $etudiant_id = htmlspecialchars(trim($_POST['etudiant_id']));
    $admin_id = $_SESSION['user_id'];

    try {
        // Insérer une absence marquée par l'admin dans justificatifs
        $stmt = $pdo->prepare("INSERT INTO justificatifs (etudiant_id, date_absence, statut, marque_par_admin, admin_id) 
                               VALUES (?, CURDATE(), 'marque_par_admin', TRUE, ?)");
        $stmt->execute([$etudiant_id, $admin_id]);
        $message = "Absence marquée avec succès.";
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
    }
} else {
    $message = "Requête invalide.";
}

header("Location: admin_dashboard.php?message=" . urlencode($message));
exit();
?>