<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

require_once '../config.php'; // doit contenir $pdo

$username = $_SESSION['username'];
$prix = 6000;

try {
    // Vérifier la monnaie actuelle
    $stmt = $pdo->prepare("SELECT money FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable']);
        exit;
    }

    $argentActuel = $user['money'];

    if ($argentActuel < $prix) {
        echo json_encode(['success' => false, 'message' => 'Pas assez de monnaie']);
        exit;
    }

    // Déduire la monnaie
    $stmt = $pdo->prepare("UPDATE users SET money = money - ? WHERE username = ?");
    $stmt->execute([$prix, $username]);

    $newBalance = $argentActuel - $prix;

    echo json_encode([
        'success' => true,
        'message' => 'Achat effectué',
        'new_balance' => $newBalance
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
}


