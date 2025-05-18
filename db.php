<?php
// db.php - Connexion sécurisée à la base de données avec PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=etudiants_app', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
