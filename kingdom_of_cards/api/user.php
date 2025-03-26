<?php
session_start();
require_once "../config.php";

function registerUser() {
    global $pdo;
    $data = json_decode(file_get_contents("php://input"), true);
    $username = trim($data["username"] ?? '');
    $email = trim($data["email"] ?? '');
    $password = trim($data["password"] ?? '');
    $confirm_password = trim($data["confirm_password"] ?? '');

    if (!$username || !$email || !$password || !$confirm_password) {
        echo json_encode(["error" => "Tous les champs sont requis."]);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Adresse email invalide."]);
        return;
    }

    if ($password !== $confirm_password) {
        echo json_encode(["error" => "Les mots de passe ne correspondent pas."]);
        return;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "Nom d'utilisateur déjà pris."]);
        return;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "Email déjà utilisé."]);
        return;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashed]);
    $user_id = $pdo->lastInsertId();

    // ✅ Donne les 5 cartes de départ
    $starterCards = [
        1 => 1, // Gobelin Pyromane
        2 => 1, // Serpent des Sables
        3 => 1, // Golem Mécanique
        4 => 1, // Chimère Sanglante
        5 => 1  // Gardien Spectral
    ];

    $stmt = $pdo->prepare("INSERT INTO joueur_cartes (id_joueur, id_carte, quantite) VALUES (?, ?, ?)");
    foreach ($starterCards as $idCarte => $quantite) {
        $stmt->execute([$user_id, $idCarte, $quantite]);
    }

    echo json_encode(["success" => "Inscription réussie"]);
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
    } else {
        echo json_encode(["error" => "Identifiants invalides"]);
    }
}

function logoutUser() {
    session_destroy();
    echo json_encode(["success" => "Déconnexion réussie"]);
}

// ROUTEUR AUTOMATIQUE
$path = $_SERVER['REQUEST_URI'];

if (strpos($path, "register") !== false) {
    registerUser();
} elseif (strpos($path, "login") !== false) {
    loginUser();
} elseif (strpos($path, "logout") !== false) {
    logoutUser();
} else {
    echo json_encode(["error" => "Route non reconnue"]);
}


