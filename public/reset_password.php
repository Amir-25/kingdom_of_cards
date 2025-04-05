<?php
require_once "../config.php";

$token = $_GET['token'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);

if ($stmt->rowCount() === 0) {
    die("Lien invalide ou expiré.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="../Styles/styles.css">
</head>
<body>
    <div class="container">
        <h2>Nouveau mot de passe</h2>
        <form action="update_password.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <label>Nouveau mot de passe :</label>
            <input type="password" name="new_password" required>
            <button type="submit">Réinitialiser</button>
        </form>
    </div>
</body>
</html>
