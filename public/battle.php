<?php
// battle.php (exemple très simplifié)
session_start();  // si vous utilisez des sessions pour identifier les joueurs
$matchId = $_GET['match'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Partie en cours</title></head>
<body>
    <h1>Combat engagé !</h1>
    <p>Identifiant de la partie : <strong><?= htmlspecialchars($matchId) ?></strong></p>
    <!-- Ici, vous pourriez établir une nouvelle connexion WebSocket dédiée au jeu 
         pour échanger les actions de chaque joueur dans cette partie. 
         Par exemple, un autre serveur ou le même Ratchet pourrait gérer les messages de jeu 
         au sein d'une "room" identifiée par $matchId. -->
</body>
</html>
