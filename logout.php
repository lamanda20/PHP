<?php
// logout.php - Déconnexion sécurisée des utilisateurs (Étudiants et Administrateurs)

// Démarrer la session
session_start();

// Supprimer toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header("Location: login.php?message=Vous avez été déconnecté avec succès.");
exit();
