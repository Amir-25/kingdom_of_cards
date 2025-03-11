<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Kingdom of Cards - Accueil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="home-container">
        <h1 class="home-title">👑 Kingdom of Cards 👑</h1>

        <div class="menu">
            <button class="menu-button" onclick="location.href='matchmaking.php'">🎴 Trouver un adversaire</button>
            <button class="menu-button" onclick="location.href='solo_mode.php'">⚔️ Mode Solo</button>
            <button class="menu-button" onclick="location.href='inventory.php'">🃏 Préparer Inventaire</button>
            <button class="menu-button logout-button" onclick="location.href='logout.php'">🚪 Se Déconnecter</button>
        </div>
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
