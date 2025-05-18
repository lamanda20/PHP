<?php
// generate_pdf.php - Génération de PDF avec les fichiers de l'étudiant

// Démarrer la session
use fpdf\FPDF;

session_start();
require_once 'db.php';

// Charger la bibliothèque FPDF
require_once 'lib/fpdf/fpdf.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Récupération de l'ID de l'étudiant connecté
$etudiant_id = $_SESSION['user_id'];

// Récupération des informations de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = ?");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();

// Récupération des fichiers de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM fichiers WHERE etudiant_id = ?");
$stmt->execute([$etudiant_id]);
$fichiers = $stmt->fetchAll();

// Création du PDF avec FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Titre du document
$pdf->Cell(0, 10, 'Rapport des Fichiers de l\'Etudiant', 0, 1, 'C');
$pdf->Ln(10);

// Informations de l'étudiant
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'Nom : ' . htmlspecialchars($etudiant['nom']));
$pdf->Ln();
$pdf->Cell(40, 10, 'Prénom : ' . htmlspecialchars($etudiant['prenom']));
$pdf->Ln();
$pdf->Cell(40, 10, 'Apogée : ' . htmlspecialchars($etudiant['apogee']));
$pdf->Ln();
$pdf->Cell(40, 10, 'Email : ' . htmlspecialchars($etudiant['email']));
$pdf->Ln(20);

// Liste des fichiers
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Liste des Fichiers :', 0, 1);
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 12);

if (count($fichiers) > 0) {
    foreach ($fichiers as $fichier) {
        $pdf->Cell(0, 10, '- ' . htmlspecialchars($fichier['nom_fichier']) . ' (téléversé le ' . $fichier['date_upload'] . ')');
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'Aucun fichier téléversé.');
}

// Génération du PDF
$pdf->Output('I', 'rapport_fichiers_' . $etudiant['apogee'] . '.pdf');
