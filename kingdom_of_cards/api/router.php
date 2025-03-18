<?php
header("Content-Type: application/json");
require_once "../config.php";
require_once "user.php";

// Récupérer l'URL de la requête
$request_uri = explode("?", $_SERVER["REQUEST_URI"], 2)[0];
$request_method = $_SERVER["REQUEST_METHOD"];

// Définition des routes
$routes = [
    "POST /register" => "registerUser",
    "POST /login" => "loginUser",
    "GET /logout" => "logoutUser"
];

// Vérifier si la route existe
$route_key = "$request_method $request_uri";
if (isset($routes[$route_key])) {
    call_user_func($routes[$route_key]);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Route not found"]);
}
