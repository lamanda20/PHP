<?php
  session_start();
  require_once 'db.php';

  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }

  $etudiant_id = $_SESSION['user_id'];
  $message = "";

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
      $fichier = $_FILES['profile_image'];
      $nom_fichier = basename($fichier['name']);
      $taille_fichier = $fichier['size'];
      $type_fichier = strtolower(pathinfo($nom_fichier, PATHINFO_EXTENSION));
      $dossier_televersement = "uploads/";
      $chemin_fichier = $dossier_televersement . uniqid() . '.' . $type_fichier;

      $types_autorises = ['jpg', 'jpeg', 'png'];
      if (!in_array($type_fichier, $types_autorises)) {
          $message = "Format de fichier non autorisé. (JPG, PNG uniquement)";
      } elseif ($taille_fichier > 2 * 1024 * 1024) { // Limite de 2 Mo
          $message = "Le fichier est trop volumineux (limite de 2 Mo).";
      } else {
          if (!is_dir($dossier_televersement)) {
              mkdir($dossier_televersement, 0777, true);
          }

          if (move_uploaded_file($fichier['tmp_name'], $chemin_fichier)) {
              // Mettre à jour la table fichiers
              $stmt = $pdo->prepare("INSERT INTO fichiers (etudiant_id, nom_fichier, date_upload) VALUES (?, ?, NOW())");
              $stmt->execute([$etudiant_id, basename($chemin_fichier)]);
              $message = "Photo de profil téléversée avec succès.";
          } else {
              $message = "Erreur lors du téléversement du fichier.";
          }
      }
  } else {
      $message = "Aucun fichier sélectionné.";
  }

  header("Location: dashboard.php?message=" . urlencode($message));
  exit();
  ?>