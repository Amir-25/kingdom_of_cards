<?php
header('Content-Type: application/json');
require_once('../config.php');

// Récupérer le chemin demandé (ex: /login)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = basename($uri); // ex: 'login'

// Dispatcher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $route === 'login') {
    handleLogin($pdo);
} else {
    echo json_encode(['success' => false, 'error' => 'Route inconnue']);
    exit;
}

// ----------------------------------
//  Fonction de connexion
// ----------------------------------
function handleLogin($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Champs requis manquants']);
        return;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            echo json_encode([
                'success' => true,
                'user' => [
                    'id' => (string) $user['id'],
                    'username' => $user['username']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Identifiants invalides']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Erreur serveur : ' . $e->getMessage()]);
    }
}
