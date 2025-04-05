<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
session_start();
require_once '../config.php';

//  Récupère le username via session, GET ou POST
$username = $_SESSION['username'] ?? $_GET['username'] ?? $_POST['username'] ?? null;

if (!$username) {
    echo json_encode(['error' => 'Nom d’utilisateur manquant']);
    exit;
}

try {
    //  Trouve l’ID du joueur à partir du username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['error' => 'Utilisateur introuvable']);
        exit;
    }

    $userId = $user['id'];

    //  Récupère les cartes de l’utilisateur
    $stmt = $pdo->prepare("
        SELECT c.id, c.nom AS name, c.image_path AS image, jc.quantite AS quantity
        FROM joueur_cartes jc
        JOIN cartes c ON jc.id_carte = c.id
        WHERE jc.id_joueur = ?
    ");
    $stmt->execute([$userId]);
    $cartes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($cartes);

} catch (PDOException $e) {
    echo json_encode(['error' => "Erreur serveur : " . $e->getMessage()]);
}
