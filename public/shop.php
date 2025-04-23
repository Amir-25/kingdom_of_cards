<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Shop - Kingdom of Cards</title>
    <link rel="stylesheet" href="../Styles/shop.css">
</head>
<body class="shop-page">
    <!-- BARRE SHOP -->
    <div class="shop-bar">
        <div class="shop-title">Magasin</div>
        <div class="user-info">
            <span class="username"><?php echo $_SESSION['username']; ?></span>
            <div class="currency"><?php
require_once '../config.php';
$username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT money FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<span id="money"><?php echo $user['money']; ?></span>
💰</div>
        </div>
    </div>

    <!-- CONTENU CENTRAL -->
    <div class="pack-section">
        <div class="pack-wrapper">
            <img src="../assets/pack_bg.png" class="pack-card" alt="Pack de cartes" onclick="confirmPurchase()">
            <img class="pack-price" src="../assets/prix.png"></img>
        </div>
    </div>

    <!-- BOÎTE DE CONFIRMATION -->
    <div id="confirmation" class="confirmation-box" style="display: none;">
        <p>Souhaitez-vous acheter ce pack pour <strong>6000</strong> 💰 ?</p>
        <button type="button" onclick="confirmBuy()">Oui</button>
        <button type="button" onclick="cancelBuy()">Non</button>
    </div>

    <!-- RÉVÉLATION DES CARTES -->
    <div id="reveal" class="reveal-box" style="display: none;">
        <div class="cards-reveal" onclick="closeReveal()">
            <!-- Carte tirée injectée ici par JS -->
        </div>
    </div>

    <!-- RETOUR -->
    <a class="retour" href="home.php">RETOUR</a>

    <!-- JS -->
    <script src="shop.js"></script>
</body>
</html>










