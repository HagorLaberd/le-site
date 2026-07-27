<?php
// traitement.php - Sauvegarde en base + envoi email

require_once '../db_connect.php';

// Inclure PHPMailer manuellement
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupérer et nettoyer les données
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $source = trim($_POST['decouverte'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation simple
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom est requis";
    if (empty($email)) $errors[] = "L'email est requis";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if (empty($message)) $errors[] = "Le message est requis";
    
    // Si pas d'erreurs, sauvegarder en base ET envoyer email
    if (empty($errors)) {
        
        // ===== 1. SAUVEGARDE EN BASE =====
        try {
            $sql = "INSERT INTO messages (nom, email, telephone, source, message, date_envoi) 
                    VALUES (:nom, :email, :telephone, :source, :message, NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':telephone' => $telephone,
                ':source' => $source,
                ':message' => $message
            ]);
            
            $base_success = true;
            
        } catch (PDOException $e) {
            $base_success = false;
            $errors[] = "Erreur base de données : " . $e->getMessage();
        }
        
        // ===== 2. ENVOI D'EMAIL =====
        try {
            $mail = new PHPMailer(true);
            
            // Configuration du serveur SMTP Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mohamedhaminhaidra@gmail.com';  // 🔴 REMPLACE ICI
            $mail->Password   = 'mqnxtrttyfmslvpr'; // 🔴 REMPLACE ICI
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            // Expéditeur et destinataire
            $mail->setFrom('TON-EMAIL@gmail.com', 'Site de contact');
            $mail->addAddress('TON-EMAIL@gmail.com', 'Toi'); // Même adresse pour recevoir
            
            // Répondre à l'expéditeur du formulaire
            $mail->addReplyTo($email, $nom);
            
            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Nouveau message de contact';
            $mail->Body    = "
                <h2>Nouveau message reçu</h2>
                <p><strong>Nom :</strong> " . htmlspecialchars($nom) . "</p>
                <p><strong>Email :</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Téléphone :</strong> " . htmlspecialchars($telephone) . "</p>
                <p><strong>Source :</strong> " . htmlspecialchars($source) . "</p>
                <p><strong>Message :</strong></p>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            ";
            $mail->AltBody = "
                Nouveau message reçu\n
                Nom : $nom\n
                Email : $email\n
                Téléphone : $telephone\n
                Source : $source\n
                Message :\n$message
            ";
            
            $mail->send();
            $email_success = true;
            
        } catch (Exception $e) {
            $email_success = false;
            $errors[] = "Erreur envoi email : " . $mail->ErrorInfo;
        }
        
        // Redirection selon le résultat
        if ($base_success && $email_success) {
            header('Location: confirmation.php?status=success');
            exit;
        } elseif ($base_success && !$email_success) {
            header('Location: confirmation.php?status=partial');
            exit;
        } else {
            // Afficher les erreurs
            echo "<h3 style='color: #cc0000;'>Erreurs :</h3><ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
            echo '<p><a href="contact-us.html">⬅ Retour au formulaire</a></p>';
            exit;
        }
    }
    
    // S'il y a des erreurs de validation
    if (!empty($errors)) {
        echo "<h3 style='color: #cc0000;'>Erreurs de validation :</h3><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo '<p><a href="contact-us.html">⬅ Retour au formulaire</a></p>';
    }
} else {
    // Accès direct
    header('Location: contact-us.html');
    exit;
}
?>