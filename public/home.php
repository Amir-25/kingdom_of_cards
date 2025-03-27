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
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="home-container">
        <h1 class="home-title">👑 Kingdom of Cards 👑</h1>

        <div class="menu">
            <button class="menu-button" onclick="location.href='matchmaking.php'">🎴 Trouver un adversaire</button>
            <button class="menu-button" onclick="location.href='solo_mode.php'">⚔️ Mode Solo</button>
            <button class="menu-button" onclick="location.href='inventory.php'">🃏 Préparer Inventaire</button>

            <button class="menu-button" onclick="location.href='shop.php'">💰 Magasin</button>
            <!-- On retire le onclick pour la déconnexion et on ajoute un id -->
            <button class="menu-button logout-button" id="logout-btn">🚪 Se Déconnecter</button>

        </div>
    </div>
    <audio id="audio-player" loop autoplay>
        <source src="../assets/home.mp3" type="audio/mpeg">
        Votre navigateur ne supporte pas l'audio.
    </audio>

    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script>
        document.getElementById("logout-btn").addEventListener("click", function() {
            fetch("../api/router.php/logout", {
                credentials: "same-origin"
            })
            .then(response => response.json())
            .then(data => {
                alert(data.success);
                window.location.href = "login.php";
            });
        });
    </script>
    <script src="audio.js"></script>
</body>
</html>
