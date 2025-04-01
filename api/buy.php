<?php
header('Content-Type: application/json');
require_once('../config.php');

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? null;
$pack_price = 6000;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
    exit;
}

try {
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

    $new_balance = $money - $pack_price;
    $pdo->prepare("UPDATE users SET money = ? WHERE id = ?")->execute([$new_balance, $user_id]);

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

    $total = array_sum(array_column($cards, "chance"));
    $rand = mt_rand(1, $total);
    foreach ($cards as $card) {
        $rand -= $card["chance"];
        if ($rand <= 0) {
            $selected = $card;
            break;
        }
    }

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
