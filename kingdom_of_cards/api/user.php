<?php
session_start();
require_once "../config.php";

// Vérifier la méthode de la requête
$request_method = $_SERVER["REQUEST_METHOD"];

// Inscription d'un nouvel utilisateur
if ($request_method === "POST" && isset($_GET["register"])) {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = trim($data["username"]);
    $password = trim($data["password"]);
    $confirm_password = trim($data["confirm_password"]);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo json_encode(["error" => "Tous les champs sont requis."]);
        exit;
    }

    if ($password !== $confirm_password) {
        echo json_encode(["error" => "Les mots de passe ne correspondent pas."]);
        exit;
    }

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "Ce nom d'utilisateur est déjà pris."]);
        exit;
    }

    // Hacher le mot de passe et enregistrer l'utilisateur
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $hashed_password])) {
        $user_id = $pdo->lastInsertId();

        // 🔥 Ajouter les 10 cartes de départ dans inventory
        $starter_cards = [1,2,3,4,5,6,7,8,9,10]; // IDs des cartes de base
        $stmt = $pdo->prepare("INSERT INTO inventory (user_id, card_id) VALUES (?, ?)");
        foreach ($starter_cards as $card_id) {
            $stmt->execute([$user_id, $card_id]);
        }

        // 🔥 Créer une entrée vide dans deck pour forcer le joueur à choisir 10 cartes
        $stmt = $pdo->prepare("DELETE FROM deck WHERE user_id = ?");
        $stmt->execute([$user_id]);

        echo json_encode(["success" => "Inscription réussie ! Inventaire créé."]);
    } else {
        echo json_encode(["error" => "Erreur lors de l'inscription."]);
    }
    exit;
}


// Connexion d'un utilisateur
if ($request_method === "POST" && isset($_GET["login"])) {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = trim($data["username"]);
    $password = trim($data["password"]);

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $username;
        echo json_encode(["success" => "Connexion réussie."]);
    } else {
        echo json_encode(["error" => "Nom d'utilisateur ou mot de passe incorrect."]);
    }
    exit;
}

// Déconnexion de l'utilisateur
if ($request_method === "GET" && isset($_GET["logout"])) {
    session_destroy();
    echo json_encode(["success" => "Déconnexion réussie."]);
    exit;
}

// Si aucune action n'est reconnue
echo json_encode(["error" => "Requête invalide."]);