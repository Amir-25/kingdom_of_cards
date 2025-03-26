<?php
header("Content-Type: application/json");
require_once "../config.php";
require_once "user.php";

// Récupère l'URI demandée et le chemin du script
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = $_SERVER['SCRIPT_NAME']; // Par exemple "/kingdom_of_cards/api/router.php"

// Retire le chemin du script pour obtenir le endpoint
$uri = str_replace($base, "", $uri);
$segments = explode("/", trim($uri, "/"));
$endpoint = $segments[0] ?? null;

$method = $_SERVER['REQUEST_METHOD'];

// Routing REST
if ($method === "POST" && $endpoint === "register") {
    registerUser();
} elseif ($method === "POST" && $endpoint === "login") {
    loginUser();
} elseif ($method === "GET" && $endpoint === "logout") {
    logoutUser();
} elseif ($method === "POST" && $endpoint === "save_deck") {
    saveDeck();
} elseif ($method === "GET" && $endpoint === "load_deck") {
    loadDeck();
} else {
    http_response_code(404);
    echo json_encode(["error" => "Route introuvable"]);
}
