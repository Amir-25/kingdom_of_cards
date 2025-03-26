<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../public/connexion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inventaire - Kingdom of Cards</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

    <!-- Slots pour le deck -->
    <div class="deck-slots">
        <?php for ($i = 0; $i < 10; $i++): ?>
            <div class="slot"></div>
        <?php endfor; ?>
    </div>

    <!-- ✅ Conteneur pour les cartes de l’inventaire -->
    <div class="card-list">
        <!-- Les cartes seront injectées ici par JS -->
    </div>

    <audio id="audio-player" loop autoplay>
        <source src="../assets/inventory.mp3" type="audio/mpeg">
        Votre navigateur ne supporte pas l'audio.
    </audio>

    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script src="audio.js"></script>
    <script src="inventory.js"></script>
</body>
</html>





