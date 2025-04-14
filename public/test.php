<?php
include 'config.php'; 

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "Il y a $count utilisateurs dans la base.";
} catch (PDOException $e) {
    echo "Erreur lors de la requête : " . $e->getMessage();
}
?>
