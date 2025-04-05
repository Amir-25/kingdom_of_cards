<?php
session_start();
header('Content-Type: application/json');
require_once('../config.php');

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? null;
$carte_id = $data['carte_id'] ?? null;
$carte_nom = $data['carte_nom'] ?? null;

file_put_contents("debug.log", "▶ ID: $carte_id | Nom: $carte_nom | Username: $username\n", FILE_APPEND);

// Déterminer l'ID du joueur
if (isset($_SESSION['user_id'])) {
    $joueur_id = $_SESSION['user_id'];
} elseif ($username) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
        exit;
    }
    $joueur_id = $user['id'];
} else {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

try {
    // Si l'ID est manquant mais le nom est fourni, on cherche l'ID à partir du nom
    if (!$carte_id && $carte_nom) {
        $stmt = $pdo->prepare("SELECT id FROM cartes WHERE TRIM(LOWER(nom)) = TRIM(LOWER(:nom))");
        $stmt->execute(['nom' => $carte_nom]);
        $carte = $stmt->fetch();
        
        if (!$carte) {
            echo json_encode(['success' => false, 'message' => 'Carte introuvable par nom']);
            exit;
        }

        $carte_id = $carte['id'];
    } elseif (!$carte_id && !$carte_nom) {
        echo json_encode(['success' => false, 'message' => 'ID ou nom manquant']);
        exit;
    }

    // Vérifie si le joueur possède déjà cette carte
    $stmt = $pdo->prepare("SELECT id, quantite FROM joueur_cartes WHERE id_joueur = :joueur_id AND id_carte = :carte_id");
    $stmt->execute(['joueur_id' => $joueur_id, 'carte_id' => $carte_id]);
    $existe = $stmt->fetch();

    if ($existe) {
        // Incrémenter la quantité
        $stmt = $pdo->prepare("UPDATE joueur_cartes SET quantite = quantite + 1 WHERE id = :id");
        $stmt->execute(['id' => $existe['id']]);
    } else {
        // Ajouter la nouvelle carte au joueur
        $stmt = $pdo->prepare("INSERT INTO joueur_cartes (id_joueur, id_carte, quantite) VALUES (:joueur_id, :carte_id, 1)");
        $stmt->execute(['joueur_id' => $joueur_id, 'carte_id' => $carte_id]);
    }

    file_put_contents("debug.log", "✅ Carte ID $carte_id sauvegardée pour joueur $joueur_id\n", FILE_APPEND);
    echo json_encode(['success' => true, 'message' => 'Carte sauvegardée']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
