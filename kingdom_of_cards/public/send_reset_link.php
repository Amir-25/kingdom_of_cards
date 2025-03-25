<?php
require_once "../config.php";
require_once "../phpmailer/PHPMailer.php";
require_once "../phpmailer/SMTP.php";
require_once "../phpmailer/Exception.php";

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];

    // Vérifie si l'email existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() === 0) {
        echo "Adresse email introuvable.";
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // Supprimer les anciens tokens de cet email
    $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

    // Insérer le token dans la base
    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $token, $expires]);

    // Envoi de l'email
    $resetLink = "http://localhost/Kingdom-of-Cards/kingdom_of_cards/public/reset_password.php?token=$token";

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'amir0408.ar@gmail.com';
    $mail->Password = 'xyvukwwrtzjsvaxi';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('amir0408.ar@gmail.com', 'Kingdom of Cards');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Réinitialisation de votre mot de passe';
    $mail->Body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$resetLink'>$resetLink</a>";

    try {
        $mail->send();
        echo "Un email de réinitialisation a été envoyé.";
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
    }
}
