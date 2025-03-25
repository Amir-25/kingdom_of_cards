<?php
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"];
    $newPassword = $_POST["new_password"];

    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);

    if ($stmt->rowCount() === 0) {
        die("Lien invalide ou expiré.");
    }

    $email = $stmt->fetchColumn();

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hashedPassword, $email]);

    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$email]);

    echo "Mot de passe réinitialisé avec succès. <a href='login.php'>Se connecter</a>";
}

