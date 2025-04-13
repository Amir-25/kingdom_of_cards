<?php
header("Content-Type: application/json");
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"), true);
$idCarte1 = $data['carte1'] ?? null;
$idCarte2 = $data['carte2'] ?? null;

if (!$idCarte1 || !$idCarte2) {
    echo json_encode(["fusion" => false, "error" => "Cartes manquantes."]);
    exit;
}

// Vérifie la fusion dans les deux sens (1-2 ou 2-1)
$stmt = $pdo->prepare("SELECT id_carte_resultat FROM fusions WHERE (id_carte_1=? AND id_carte_2=?) OR (id_carte_1=? AND id_carte_2=?)");
$stmt->execute([$idCarte1, $idCarte2, $idCarte2, $idCarte1]);
$fusion = $stmt->fetch();

if ($fusion) {
    // Récupère infos carte résultante
    $stmt = $pdo->prepare("SELECT * FROM cartes WHERE id=?");
    $stmt->execute([$fusion['id_carte_resultat']]);
    $carteFusionnee = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(["fusion" => true, "carte" => $carteFusionnee]);
} else {
    echo json_encode(["fusion" => false, "error" => "Ces deux cartes ne peuvent pas fusionner."]);
}
