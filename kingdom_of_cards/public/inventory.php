<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../config.php";

$user_id = $_SESSION["user_id"];

// Récupérer les cartes du joueur depuis l'inventaire
$stmt = $pdo->prepare("
    SELECT c.id, c.name, c.image 
    FROM inventory i
    JOIN cards c ON i.card_id = c.id
    WHERE i.user_id = ?
");
$stmt->execute([$user_id]);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Kingdom of Cards - Inventaire</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="audio.js" defer></script>
    <script src="inventory.js" defer></script>
</head>
<body>
    <div class="inventory-container">
        <h1>🃏 Prépare ton Deck</h1>

        <!-- Zone du deck (10 slots visibles) -->
        <div class="deck">
            <h2>Deck sélectionné (10 cartes)</h2>
            <div class="deck-slots">
                <?php for ($i = 0; $i < 10; $i++): ?>
                    <div class="slot" data-index="<?= $i ?>"></div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Cartes disponibles dans l'inventaire -->
        <div class="cards">
            <h2>Cartes disponibles</h2>
            <div class="card-list">
                <?php foreach ($cards as $card): ?>
                    <div class="card" draggable="true" data-id="<?= $card['id']; ?>">
                        <img src="<?= htmlspecialchars($card['image']); ?>" alt="<?= htmlspecialchars($card['name']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button id="save-deck">✅ Sauvegarder le Deck</button>
        <button onclick="location.href='home.php'">🏠 Retour à l'accueil</button>
    </div>
</body>
</html>
