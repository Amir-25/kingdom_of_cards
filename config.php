<?php
$host = "kingdommysqlserver.mysql.database.azure.com"; 
$dbname = "kingdom";
$username = "adminkingdom";
$password = "kingdom123?"; 

// téléchargé depuis Azure
$ssl_cert_path = "C:/xampp/htdocs/certifs/DigiCertGlobalRootCA.crt.pem";

try {
    // Connexion sécurisée avec certificat SSL
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8;sslmode=require",
        $username,
        $password,
        array(
            PDO::MYSQL_ATTR_SSL_CA => $ssl_cert_path
        )
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
