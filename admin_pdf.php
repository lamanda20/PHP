<?php
// admin_pdf.php - Génération de PDF pour les Administrateurs

session_start();
require_once 'db.php';

// Charger la bibliothèque FPDF
$fpdf_path = __DIR__ . '/lib/fpdf/fpdf.php';
if (file_exists($fpdf_path)) {
    require_once $fpdf_path;
} else {
    die("Erreur : La bibliothèque FPDF n'a pas été trouvée.");
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$id_filiere = isset($_GET['id_filiere']) ? intval($_GET['id_filiere']) : null;
$id_module = isset($_GET['id_module']) ? intval($_GET['id_module']) : null;

$where = "WHERE j.marque_par_admin = TRUE OR j.statut IN ('en_attente', 'valide', 'rejete')";
$params = [];
if ($id_filiere) {
    $where .= " AND m.id_filiere = ?";
    $params[] = $id_filiere;
}
if ($id_module) {
    $where .= " AND j.module_id = ?";
    $params[] = $id_module;
}

$stmt = $pdo->prepare("SELECT e.nom, e.prenom, e.apogee, m.nom_module, f.nom_filiere, j.date_absence, j.fichier_path, j.statut 
                       FROM justificatifs j 
                       JOIN etudiants e ON j.etudiant_id = e.id 
                       JOIN modules m ON j.module_id = m.id_module 
                       JOIN filieres f ON m.id_filiere = f.id_filiere 
                       $where 
                       ORDER BY f.nom_filiere, m.nom_module, e.nom, e.prenom");
$stmt->execute($params);
$absences = $stmt->fetchAll();

$pdf_dir = __DIR__ . '/pdf/';
if (!is_dir($pdf_dir)) {
    mkdir($pdf_dir, 0777, true);
}

class PDF extends FPDF {
    function Header() {
        $logo_path = __DIR__ . '/images/logo.png';
        if (file_exists($logo_path)) {
            $this->Image($logo_path, 10, 6, 30);
        }
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Rapport des Absences', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Généré le ' . date('d/m/Y H:i'), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255);

$pdf->Cell(30, 10, 'Nom', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Prénom', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Apogée', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Module', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Filière', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Date Absence', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Justificatif', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Statut', 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
foreach ($absences as $absence) {
    $pdf->Cell(30, 10, htmlspecialchars($absence['nom'] ?? ''), 1);
    $pdf->Cell(30, 10, htmlspecialchars($absence['prenom'] ?? ''), 1);
    $pdf->Cell(30, 10, htmlspecialchars($absence['apogee'] ?? ''), 1);
    $pdf->Cell(40, 10, htmlspecialchars($absence['nom_module'] ?? ''), 1);
    $pdf->Cell(30, 10, htmlspecialchars($absence['nom_filiere'] ?? ''), 1);
    $pdf->Cell(30, 10, htmlspecialchars($absence['date_absence'] ?? ''), 1);
    $pdf->Cell(20, 10, $absence['fichier_path'] ? '✅' : '❌', 1);
    $pdf->Cell(20, 10, htmlspecialchars($absence['statut'] ?? ''), 1);
    $pdf->Ln();
}

$filiere_filter = $id_filiere ? $pdo->query("SELECT nom_filiere FROM filieres WHERE id_filiere = $id_filiere")->fetch()['nom_filiere'] ?? 'toutes' : 'toutes';
$module_filter = $id_module ? $pdo->query("SELECT nom_module FROM modules WHERE id_module = $id_module")->fetch()['nom_module'] ?? 'tous' : 'tous';
$pdf_filename = $pdf_dir . 'rapport_absences_' . str_replace(' ', '_', $filiere_filter) . '_' . str_replace(' ', '_', $module_filter) . '_' . date('Ymd_Hi') . '.pdf';
$pdf->Output('F', $pdf_filename);
$pdf->Output('I', 'rapport_absences_' . str_replace(' ', '_', $filiere_filter) . '_' . str_replace(' ', '_', $module_filter) . '_' . date('Ymd_Hi') . '.pdf');
exit();
?>