<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ROUTEUR AUTOMATIQUE
$path = $_SERVER['REQUEST_URI'];

if (strpos($path, "register") !== false) {
    registerUser();
} elseif (strpos($path, "login") !== false) {
    loginUser();
} elseif (strpos($path, "logout") !== false) {
    logoutUser();
} elseif (strpos($path, "save_deck") !== false) {
    saveDeck();
} elseif (strpos($path, "load_deck") !== false) {
    loadDeck();
} elseif (strpos($path, "get_money") !== false) {
    getMoney();
} else {
    echo json_encode(["error" => "Route non reconnue"]);
    exit;
}

function registerUser() {
    global $pdo;
    $data = json_decode(file_get_contents("php://input"), true);

    $username = trim($data["username"] ?? '');
    $email = trim($data["email"] ?? '');
    $password = trim($data["password"] ?? '');
    $confirm_password = trim($data["confirm_password"] ?? '');

    if (!$username || !$email || !$password || !$confirm_password) {
        echo json_encode(["error" => "Tous les champs sont requis."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Adresse email invalide."]);
        exit;
    }

    if ($password !== $confirm_password) {
        echo json_encode(["error" => "Les mots de passe ne correspondent pas."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "Nom d'utilisateur déjà pris."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "Email déjà utilisé."]);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashed]);
    $user_id = $pdo->lastInsertId();

    // Donne les cartes de départ
    $starterCards = [
        1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1
    ];

    $stmt = $pdo->prepare("INSERT INTO joueur_cartes (id_joueur, id_carte, quantite) VALUES (?, ?, ?)");
    foreach ($starterCards as $idCarte => $quantite) {
        $stmt->execute([$user_id, $idCarte, $quantite]);
    }

    echo json_encode(["success" => "Inscription réussie"]);
    exit;
}

function loginUser() {
    global $pdo;
    $data = json_decode(file_get_contents("php://input"), true);

    $username = trim($data["username"] ?? '');
    $password = trim($data["password"] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(["error" => "Champs manquants"]);
        return;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        echo json_encode([
            "success" => true,
            "user" => [
                "username" => $user["username"],
                "email" => $user["email"],
                "money" => $user["money"]
            ]
        ]);
        exit;
    } else {
        echo json_encode(["error" => "Identifiants invalides"]);
        exit;
    }
}

function logoutUser() {
    session_destroy();
    echo json_encode(["success" => "Déconnexion..."]);
    exit;
}

function saveDeck() {
    global $pdo;

    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["error" => "Non connecté"]);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $_SESSION["user_id"];
    $deck = $data["deck"] ?? [];

    $pdo->prepare("DELETE FROM deck WHERE user_id = ?")->execute([$user_id]);

    if (empty($deck)) {
        echo json_encode(["error" => "Aucune carte à sauvegarder"]);
        exit;
    }
        $cardCounts = [];
        foreach ($deck as $entry) {
            $filename = basename($entry["src"]);
            $cardCounts[$filename] = ($cardCounts[$filename] ?? 0) + 1;
        }

       
        $pdo->prepare("DELETE FROM deck WHERE user_id = ?")->execute([$user_id]);

        $stmt = $pdo->prepare("INSERT INTO deck (user_id, card_id, position) VALUES (?, ?, ?)");
        $seen = [];

        foreach ($deck as $entry) {
            $src = $entry["src"];
            $position = $entry["position"];
            $filename = basename($src);

            $query = $pdo->prepare("SELECT id FROM cartes WHERE image_path LIKE ?");
            $query->execute(["%$filename"]);
            $card = $query->fetch();

            if ($card) {
                $cardId = $card["id"];
                $stmt->execute([$user_id, $cardId, $position]);

                if (!isset($seen[$cardId])) {
                    $seen[$cardId] = 0;
                }

                if ($seen[$cardId] < $cardCounts[$filename]) {
                    $seen[$cardId]++;

                    $check = $pdo->prepare("SELECT quantite FROM joueur_cartes WHERE id_joueur = ? AND id_carte = ?");
                    $check->execute([$user_id, $cardId]);
                    $owned = $check->fetchColumn();

                    if ($owned > $seen[$cardId]) {
                        $pdo->prepare("UPDATE joueur_cartes SET quantite = quantite - 1 WHERE id_joueur = ? AND id_carte = ?")
                            ->execute([$user_id, $cardId]);
                    } else {
                        $pdo->prepare("DELETE FROM joueur_cartes WHERE id_joueur = ? AND id_carte = ?")
                            ->execute([$user_id, $cardId]);
                    }
                }
            }
        }


    echo json_encode(["success" => "Deck sauvegardé avec succès"]);
    exit;
}

function loadDeck() {
    global $pdo;

    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["error" => "Non connecté"]);
        exit;
    }

    $user_id = $_SESSION["user_id"];

    $stmt = $pdo->prepare("
        SELECT d.position, c.image_path, c.nom
        FROM deck d
        JOIN cartes c ON d.card_id = c.id
        WHERE d.user_id = ?
        ORDER BY d.position ASC
    ");
    $stmt->execute([$user_id]);
    $deck = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["deck" => $deck]);
    exit;
}

function getMoney() {
    global $pdo;

    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data || !isset($data["username"]) || empty(trim($data["username"]))) {
        echo json_encode(["success" => false, "message" => "Nom d'utilisateur manquant"]);
        exit;
    }

    $username = trim($data["username"]);

    $stmt = $pdo->prepare("SELECT money FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Utilisateur introuvable"]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "money" => (int)$user["money"]
    ]);
    exit;
}