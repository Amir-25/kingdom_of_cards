<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

require_once '../config.php';

try {
    $stmt = $pdo->prepare("
        SELECT c.id, c.nom AS name, c.image_path AS image, jc.quantite AS quantity
        FROM joueur_cartes jc
        JOIN cartes c ON jc.id_carte = c.id
        WHERE jc.id_joueur = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cartes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cartes);
} catch (PDOException $e) {
    echo json_encode(['error' => "Erreur serveur : " . $e->getMessage()]);
}


