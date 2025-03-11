<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs sont requis.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si le nom d'utilisateur est déjà pris
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Ce nom d'utilisateur est déjà pris.";
        } else {
            // Hachage du mot de passe et insertion
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed_password])) {
                header("Location: login.php");
                exit;
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Kingdom of Cards</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Rejoignez le Royaume des Cartes</h2>
        <form method="post">
            <label>Nom d'utilisateur :</label>
            <input type="text" name="username" required>
            
            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <label>Confirmer le mot de passe :</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">S'inscrire</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <p>Déjà un compte ? <a href="login.php">Connecte-toi ici</a></p>
    </div>
    <audio id="audio-player" loop autoplay>
        <source src="assets/background.mp3" type="audio/mpeg">
        Votre navigateur ne supporte pas l'audio.
    </audio>

    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const audio = document.getElementById("audio-player");
            const volumeSlider = document.getElementById("volume");

            // Play audio
            audio.volume = 0.5; // Volume par défaut
            audio.play().catch(error => console.log("Autoplay bloqué par le navigateur :", error));

            // Modifier le volume
            volumeSlider.addEventListener("input", function () {
                audio.volume = this.value;
            });
        });
    </script>
</body>
</html>

