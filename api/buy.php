<?php
session_start();
header('Content-Type: application/json');
require_once('../config.php');

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$user_id = $_SESSION['user_id'];
$pack_price = 10000;

try {
    // Récupère l'argent du joueur
    $stmt = $pdo->prepare("SELECT money FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $money = $stmt->fetchColumn();

    if ($money === false) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable']);
        exit;
    }

    if ($money < $pack_price) {
        echo json_encode(['success' => false, 'message' => 'Fonds insuffisants']);
        exit;
    }

    // Déduction
    $new_balance = $money - $pack_price;
    $stmt = $pdo->prepare("UPDATE users SET money = ? WHERE id = ?");
    $stmt->execute([$new_balance, $user_id]);

    // Liste des cartes (doit correspondre à ton JS)
    $cards = [
        ["id" => 1, "name" => "Gobelin Pyromane", "chance" => 62],
        ["id" => 2, "name" => "Serpent des Sables", "chance" => 62],
        ["id" => 3, "name" => "Golem Mécanique", "chance" => 62],
        ["id" => 4, "name" => "Chimère Sanglante", "chance" => 20],
        ["id" => 5, "name" => "Gardien Spectral", "chance" => 20],
        ["id" => 6, "name" => "Dragon du Néant", "chance" => 12],
        ["id" => 7, "name" => "Chevalier de la Faille", "chance" => 12],
        ["id" => 8, "name" => "Roi des Profondeurs", "chance" => 5],
        ["id" => 9, "name" => "Titan du Néant", "chance" => 5],
        ["id" => 10, "name" => "Seigneur du Chaos Abyssal", "chance" => 1],
    ];

    // Tirage pondéré
    $total = array_sum(array_column($cards, "chance"));
    $rand = mt_rand(1, $total);
    $selected = null;
    foreach ($cards as $card) {
        $rand -= $card["chance"];
        if ($rand <= 0) {
            $selected = $card;
            break;
        }
    }

    if (!$selected) {
        echo json_encode(['success' => false, 'message' => 'Erreur de tirage de carte']);
        exit;
    }

    // Retourne les infos pour le JS
    echo json_encode([
        'success' => true,
        'message' => 'Achat effectué',
        'new_balance' => $new_balance,
        'card' => $selected["name"],
        'card_id' => $selected["id"]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}


