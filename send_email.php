<?php
// send_email.php - Envoi d'email avec PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Charger PHPMailer via Composer

function sendActivationEmail($email, $prenom, $code_activation) {
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP (Gmail)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tahaghadi3@gmail.com'; // Remplacez par votre adresse Gmail
        $mail->Password = 'sxjo fseb fbmz vxiv'; // Utilisez un mot de passe d'application (voir ci-dessous)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Paramètres de l'email
        $mail->setFrom('VOTRE_ADRESSE_EMAIL@gmail.com', 'Plateforme Étudiants');
        $mail->addAddress($email, $prenom);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = 'Activation de votre compte - Plateforme Étudiants';
        $mail->Body    = "<h2>Bonjour $prenom,</h2>
                          <p>Merci de vous être inscrit sur notre plateforme.</p>
                          <p>Votre code d'activation est : <strong>$code_activation</strong></p>
                          <p>Cliquez sur le lien suivant pour activer votre compte :</p>
                          <a href='http://localhost/GHADI_Taha/verify_code.php'>Activer mon compte</a>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
