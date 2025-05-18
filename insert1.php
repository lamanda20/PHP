<?php
session_start();
$server = "localhost";
$username = "root";
$password = "";
$dbname = "etudiants_app";

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    $_SESSION['error'] = "Connection failed: " . $conn->connect_error;
    header("location: scanner.php");
    exit();
}

if(isset($_POST['text'])) {
    $text = trim($_POST['text']);

    if (!empty($text)) {
        $stmt = $conn->prepare("INSERT INTO table_attendance(student_id, time_in) VALUES (?, NOW())");
        $stmt->bind_param("s", $text);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Présence enregistrée avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement de la présence.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Le QR Code est vide.";
    }
}

$conn->close();
header("location: scanner.php");
exit();
