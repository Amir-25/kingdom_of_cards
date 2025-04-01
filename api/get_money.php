<?php
header('Content-Type: application/json');
require_once('../config.php');

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $data['user_id'] ?? null;

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

    echo json_encode(['success' => true, 'money' => $money]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
