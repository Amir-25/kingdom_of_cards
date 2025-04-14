<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$reward = isset($data['reward']) ? intval($data['reward']) : 0;

if ($reward <= 0) {
    echo json_encode(['success' => false, 'error' => 'Montant invalide']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET money = money + ? WHERE id = ?");
    $stmt->execute([$reward, $_SESSION['user_id']]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
