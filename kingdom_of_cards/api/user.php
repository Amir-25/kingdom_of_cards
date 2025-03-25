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

    $starter_cards = [1,2,3,4,5,6,7,8,9,10];
    $stmt = $pdo->prepare("INSERT INTO inventory (user_id, card_id) VALUES (?, ?)");
    foreach ($starter_cards as $card_id) {
        $stmt->execute([$user_id, $card_id]);
    }

    echo json_encode(["success" => "Inscription réussie"]);
}

function loginUser() {
    global $pdo;
    $data = json_decode(file_get_contents("php://input"), true);
    $username = trim($data["username"] ?? '');
    $password = trim($data["password"] ?? '');

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $username;
        echo json_encode(["success" => "Connexion réussie"]);
    } else {
        echo json_encode(["error" => "Identifiants invalides"]);
    }
}

function logoutUser() {
    session_destroy();
    echo json_encode(["success" => "Déconnexion réussie"]);
}

function saveDeck() {
    global $pdo;
    session_start();
    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["error" => "Non connecté"]);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = $_SESSION["user_id"];
    $deck = $data["deck"] ?? [];

    // Vider l'ancien deck
    $pdo->prepare("DELETE FROM deck WHERE user_id = ?")->execute([$user_id]);

    // Enregistrer le nouveau
    $stmt = $pdo->prepare("INSERT INTO deck (user_id, card_id, position) VALUES (?, ?, ?)");

    foreach ($deck as $entry) {
        $src = $entry["src"];
        $position = $entry["position"];

        // Extraire le nom de fichier pour chercher l'ID
        $filename = basename($src); // exemple : golem_apocalypse.jpg
        $query = $pdo->prepare("SELECT id FROM cards WHERE image LIKE ?");
        $query->execute(["%$filename"]);
        $card = $query->fetch();

        if ($card) {
            $stmt->execute([$user_id, $card["id"], $position]);
        }
    }

    echo json_encode(["success" => "Deck sauvegardé avec succès"]);
}

function loadDeck() {
    global $pdo;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    

    if (!isset($_SESSION["user_id"])) {
        echo json_encode(["error" => "Non connecté"]);
        return;
    }

    $user_id = $_SESSION["user_id"];
    $stmt = $pdo->prepare("
        SELECT d.position, c.image, c.name
        FROM deck d
        JOIN cards c ON d.card_id = c.id
        WHERE d.user_id = ?
        ORDER BY d.position ASC
    ");
    $stmt->execute([$user_id]);
    $deck = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["deck" => $deck]);
}
