<?php
require __DIR__ . '/vendor/autoload.php';
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Matchmaker implements MessageComponentInterface {
    private $waiting = [];

    public function onOpen(ConnectionInterface $conn) {
        // Affiche un log à chaque nouvelle connexion
        echo "[" . date('H:i:s') . "] Nouvelle connexion (ID {$conn->resourceId})\n";

        if (empty($this->waiting)) {
            $this->waiting[] = $conn;
            $conn->send(json_encode(['action'=>'status','message'=>'En attente d’un adversaire…']));
        } else {
            $opponent = array_shift($this->waiting);
            $matchId  = uniqid('match_');
            $msg = json_encode(['action'=>'matchFound','matchId'=>$matchId]);

            // Log de l’appariement
            echo "[" . date('H:i:s') . "] Match trouvé : {$matchId} entre {$opponent->resourceId} et {$conn->resourceId}\n";

            $opponent->send($msg);
            $conn->send($msg);
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // (optionnel) log message
        echo "[" . date('H:i:s') . "] Message reçu de {$from->resourceId}: {$msg}\n";
    }

    public function onClose(ConnectionInterface $conn) {
        // Log de la fermeture
        echo "[" . date('H:i:s') . "] Connexion fermée (ID {$conn->resourceId})\n";

        foreach ($this->waiting as $i => $c) {
            if ($c === $conn) {
                unset($this->waiting[$i]);
            }
        }
        $this->waiting = array_values($this->waiting);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "[" . date('H:i:s') . "] Erreur sur {$conn->resourceId} : {$e->getMessage()}\n";
        $conn->close();
    }
}

$port = 8080;
$server = IoServer::factory(
    new HttpServer(new WsServer(new Matchmaker())),
    $port
);

echo "=== Serveur de matchmaking démarré sur le port {$port} ===\n";
$server->run();
