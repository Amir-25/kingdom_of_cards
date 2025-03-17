<?php
session_start();
require_once "../config.php";

// Inscription
function registerUser() {
    global $pdo;

    $data = json_decode(file_get_contents("php://input"), true);
    $username = trim($data["username"]);
    $password = trim($data["password"]);
    $confirm_password = trim($data["confirm_password"]);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        echo json_encode(["error" => "Tous les champs sont requis."]);
        return;
    }
    
    if ($password !== $confirm_password) {
        echo json_encode(["error" => "Les mots de passe ne correspondent pas."]);
        return;
    }

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(["error" => "Ce nom d'utilisateur est déjà pris."]);
        return;
    }

    // Hacher le mot de passe et enregistrer l'utilisateur
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $hashed_password])) {
        echo json_encode(["success" => "Inscription réussie."]);
    } else {
        echo json_encode(["error" => "Erreur lors de l'inscription."]);
    }
}

// Connexion
function loginUser() {
    global $pdo;

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
}

// Déconnexion
function logoutUser() {
    session_destroy();
    echo json_encode(["success" => "Déconnexion réussie."]);
}
