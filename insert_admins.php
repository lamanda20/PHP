<?php
// insert_admins.php - Script pour insérer des administrateurs avec mot de passe sécurisé
require_once 'db.php';

// Mot de passe en clair
$password = 'admin123';

// Hachage du mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertion des administrateurs avec mot de passe haché
$stmt = $pdo->prepare("INSERT INTO administrateurs (nom, email, password) VALUES 
    ('Admin1', 'admin1@gmail.com', ?),
    ('Admin2', 'admin2@gmail.com', ?)");

$stmt->execute([$hashed_password, $hashed_password]);

echo "Administrateurs insérés avec succès avec mot de passe sécurisé.<br>";
echo "Mot de passe : admin123 (haché)";
echo "<br><a href='admin_dashboard.php'>Accéder à la Connexion Admin</a>";
