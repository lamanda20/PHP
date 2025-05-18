<?php
// admin_pdf.php - Génération de PDF pour les Administrateurs

// Démarrer la session
use fpdf\FPDF;

session_start();
require_once 'db.php';

// Charger la bibliothèque FPDF avec un chemin sécurisé
$fpdf_path = __DIR__ . '/lib/fpdf/fpdf.php';
if (file_exists($fpdf_path)) {
    require_once $fpdf_path;
} else {
    die("Erreur : La bibliothèque FPDF n'a pas été trouvée.");
}

// Vérifier si l'utilisateur est connecté et est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Récupération de tous les étudiants et leurs fichiers
$stmt = $pdo->query("SELECT e.nom, e.prenom, e.apogee, e.email, f.nom_fichier, f.date_upload 
                     FROM etudiants e 
                     LEFT JOIN fichiers f ON e.id = f.etudiant_id 
                     ORDER BY e.nom, e.prenom");
$etudiants = $stmt->fetchAll();

// Création du dossier 'pdf' s'il n'existe pas
$pdf_dir = __DIR__ . '/pdf/';
if (!is_dir($pdf_dir)) {
    mkdir($pdf_dir, 0777, true);
}

// Génération du PDF avec FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Rapport des Étudiants et de leurs Fichiers', 0, 1, 'C');
$pdf->Ln(10);

// En-tête du tableau
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(40, 10, 'Nom', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Prénom', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Apogée', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Email', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Fichier', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Date Upload', 1, 0, 'C', true);
$pdf->Ln();

// Contenu du tableau
$pdf->SetFont('Arial', '', 12);

foreach ($etudiants as $etudiant) {
    $pdf->Cell(40, 10, htmlspecialchars($etudiant['nom']), 1);
    $pdf->Cell(40, 10, htmlspecialchars($etudiant['prenom']), 1);
    $pdf->Cell(30, 10, htmlspecialchars($etudiant['apogee']), 1);
    $pdf->Cell(50, 10, htmlspecialchars($etudiant['email']), 1);
    $pdf->Cell(60, 10, $etudiant['nom_fichier'] ? htmlspecialchars($etudiant['nom_fichier']) : 'Aucun fichier', 1);
    $pdf->Cell(30, 10, $etudiant['date_upload'] ?? '-', 1);
    $pdf->Ln();
}

// Enregistrer le PDF dans le dossier 'pdf'
$pdf_filename = $pdf_dir . 'rapport_etudiants_fichiers.pdf';
$pdf->Output('F', $pdf_filename);

// Redirection Automatique vers le fichier PDF
header("Location: pdf/rapport_etudiants_fichiers.pdf");
exit();
