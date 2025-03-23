<?php
require_once "../config.php";
session_start();

$user_id = $_SESSION["user_id"];

// Charger les cartes du joueur
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $stmt = $pdo->prepare("SELECT c.id, c.name, c.type, c.attack, c.defense, c.image 
                           FROM inventory i
                           JOIN cards c ON i.card_id = c.id
                           WHERE i.user_id = ?");
    $stmt->execute([$user_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// Sauvegarder le deck
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (count($data["deck"]) !== 10) {
        echo json_encode(["error" => "Le deck doit contenir exactement 10 cartes."]);
        exit;
    }

    // Supprimer l'ancien deck
    $stmt = $pdo->prepare("DELETE FROM deck WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Insérer les nouvelles cartes
    $stmt = $pdo->prepare("INSERT INTO deck (user_id, card_id, position) VALUES (?, ?, ?)");
    foreach ($data["deck"] as $index => $card_id) {
        $stmt->execute([$user_id, $card_id, $index + 1]);
    }

    echo json_encode(["success" => "Deck sauvegardé avec succès."]);
}
?>
