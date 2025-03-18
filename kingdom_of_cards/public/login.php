<?php
require_once "../config.php";
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
    <link rel="stylesheet" href="../styles.css">
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
    <audio id="audio-player" loop autoplay>
        <source src="../assets/background.mp3" type="audio/mpeg">
        Votre navigateur ne supporte pas l'audio.
    </audio>

    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script>

        document.getElementById("login-form").addEventListener("submit", function(event) {
            event.preventDefault();
        
            const formData = {
                username: document.querySelector("input[name='username']").value,
                password: document.querySelector("input[name='password']").value
            };

            fetch("/api/router.php/login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "home.php";
                } else {
                    alert(data.error);
                }
            });
        });        

        /*document.addEventListener("DOMContentLoaded", function () {
            const audio = document.getElementById("audio-player");
            const volumeSlider = document.getElementById("volume");

            // Play audio
            audio.volume = 0.5; // Volume par défaut
            audio.play().catch(error => console.log("Autoplay bloqué par le navigateur :", error));

            // Modifier le volume
            volumeSlider.addEventListener("input", function () {
                audio.volume = this.value;
            });
        });*/
    </script>
        <script src="audio.js"> 
    </script>
</body>
</html>

