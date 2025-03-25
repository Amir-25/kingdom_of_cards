<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

require_once '../config.php'; // $pdo

$data = json_decode(file_get_contents('php://input'), true);
$carte_nom = $data['carte_nom'] ?? null;

if (!$carte_nom) {
    echo json_encode(['success' => false, 'message' => 'Carte manquante']);
    exit;
}

$username = $_SESSION['username'];

// ✅ Trouver l'ID du joueur
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Joueur introuvable']);
    exit;
}

$id_joueur = $user['id'];

// ✅ Trouver l'ID de la carte à partir de son nom
$stmt = $pdo->prepare("SELECT id FROM cartes WHERE nom = ?");
$stmt->execute([$carte_nom]);
$carte = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carte) {
    echo json_encode(['success' => false, 'message' => 'Carte introuvable']);
    exit;
}

$id_carte = $carte['id'];

// ✅ Vérifier si la carte existe déjà dans l’inventaire du joueur
$stmt = $pdo->prepare("SELECT quantite FROM joueur_cartes WHERE id_joueur = ? AND id_carte = ?");
$stmt->execute([$id_joueur, $id_carte]);
$existe = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    // ✅ Mise à jour de la quantité
    $stmt = $pdo->prepare("UPDATE joueur_cartes SET quantite = quantite + 1 WHERE id_joueur = ? AND id_carte = ?");
    $stmt->execute([$id_joueur, $id_carte]);
} else {
    // ✅ Ajout d'une nouvelle ligne
    $stmt = $pdo->prepare("INSERT INTO joueur_cartes (id_joueur, id_carte, quantite) VALUES (?, ?, 1)");
    $stmt->execute([$id_joueur, $id_carte]);
}

echo json_encode(['success' => true, 'message' => 'Carte enregistrée']);

