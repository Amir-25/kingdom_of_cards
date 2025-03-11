<?php
require_once "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $username;
        header("Location: home.php");
        exit;
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Kingdom of Cards</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Entrez dans l'Arène</h2>
        <form method="post">
            <label>Nom d'utilisateur :</label>
            <input type="text" name="username" required>
            
            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <p>Pas encore de compte ? <a href="register.php">Rejoins le Royaume</a></p>
    </div>
</body>
</html>

