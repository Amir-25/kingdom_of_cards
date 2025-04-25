<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Matchmaker implements MessageComponentInterface {
    private $clients;
    private $client1;
    private $client2;
    private $waiting = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        echo "[" . date('H:i:s') . "] Nouvelle connexion (ID {$conn->resourceId})\n";
        $this->clients->attach($conn);

        if (empty($this->waiting)) {
            $this->waiting[] = $conn;
            $conn->send(json_encode([
                'action' => 'status',
                'message' => 'En attente d’un adversaire…',
                'connId' => $conn->resourceId
            ]));
            $this->client1 = $conn;
        } else {
            $opponent = array_shift($this->waiting);
            $matchId  = uniqid('match_');
            $msg = json_encode([
                'action' => 'matchFound',
                'matchId' => $matchId,
                'connId' => $conn->resourceId
            ]);
            $this->client2 = $conn;
            echo "[" . date('H:i:s') . "] Match trouvé : {$matchId} entre {$opponent->resourceId} et {$conn->resourceId}\n";

            $opponent->send($msg);
            $conn->send($msg);
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "[" . date('H:i:s') . "] Message reçu de {$from->resourceId}: {$msg}\n";

        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        echo "[" . date('H:i:s') . "] Connexion fermée (ID {$conn->resourceId})\n";

        // Supprimer de la liste d’attente
        foreach ($this->waiting as $i => $c) {
            if ($c === $conn) {
                unset($this->waiting[$i]);
            }
        }
        $this->waiting = array_values($this->waiting);

        // Supprimer de la liste des clients connectés
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "[" . date('H:i:s') . "] Erreur sur {$conn->resourceId} : {$e->getMessage()}\n";
        $conn->close();
        $this->clients->detach($conn);
    }
}

$port = 8080;
$server = IoServer::factory(
    new HttpServer(new WsServer(new Matchmaker())),
    $port
);

echo "=== Serveur de matchmaking démarré sur le port {$port} ===\n";
$server->run();
