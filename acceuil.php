<?php
// accueil.php - Page d'accueil de l'application Étudiant
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Accueil - Plateforme Étudiante</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h1>Bienvenue sur la Plateforme Étudiante</h1>

    <div class="menu">
        <a href="http://localhost:63342/PHP/register.php" class="btn">🔹 Inscription Étudiant</a>
        <a href="http://localhost:63342/PHP/login.php" class="btn">🔹 Connexion Étudiant</a>
        <a href="http://localhost:63342/PHP/admin_dashboard.php" class="btn">🔹 Connexion Administrateur</a>
        <a href="http://localhost:63342/PHP/scanner.php" class="btn">🔹 Marquer ma présence</a>
    </div>


    <div class="about-section">
        <h3>À propos de la plateforme :</h3>
        <p>Cette plateforme permet aux étudiants de s'inscrire, de soumettre des fichiers,
            et de gérer leurs documents académiques. Les administrateurs peuvent gérer les étudiants et les fichiers soumis.</p>
    </div>

    <div class="features-section">
        <h3>Fonctionnalités :</h3>
        <ul class="features-list">
            <li>🔹 Inscription avec code d'activation par email</li>
            <li>🔹 Connexion sécurisée avec mot de passe</li>
            <li>🔹 Soumission de fichiers par les étudiants</li>
            <li>🔹 Gestion des étudiants et des fichiers par les administrateurs</li>
        </ul>
    </div>

    <footer class="footer">
        <h3>Développeur :</h3>
        <p>Projet développé par:<strong>
                GHADI Taha<br>
                AIT-ABID Younes<br>
                ADLOUNE Malak<br>
                BELFADLI Hamza
            </strong>.</p>
    </footer>
</div>

</body>
</html>

