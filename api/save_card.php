<?php
header('Content-Type: application/json');
require_once('../config.php');

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? null;
$carte_id = $data['carte_id'] ?? null;
$carte_nom = $data['carte_nom'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
    exit;
}

try {
    if (!$carte_id && $carte_nom) {
        $stmt = $pdo->prepare("SELECT id FROM cartes WHERE TRIM(LOWER(nom)) = TRIM(LOWER(:nom))");
        $stmt->execute(['nom' => $carte_nom]);
        $carte = $stmt->fetch();
        if (!$carte) {
            echo json_encode(['success' => false, 'message' => 'Carte introuvable']);
            exit;
        }
        $carte_id = $carte['id'];
    } elseif (!$carte_id) {
        echo json_encode(['success' => false, 'message' => 'Carte ID manquant']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM joueur_cartes WHERE id_joueur = :user AND id_carte = :carte");
    $stmt->execute(['user' => $user_id, 'carte' => $carte_id]);
    $existe = $stmt->fetch();

    if ($existe) {
        $pdo->prepare("UPDATE joueur_cartes SET quantite = quantite + 1 WHERE id = :id")->execute(['id' => $existe['id']]);
    } else {
        $pdo->prepare("INSERT INTO joueur_cartes (id_joueur, id_carte, quantite) VALUES (:user, :carte, 1)")
            ->execute(['user' => $user_id, 'carte' => $carte_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Carte sauvegardée']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
