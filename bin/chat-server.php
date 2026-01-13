<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/Database.php';
require __DIR__ . '/../app/models/Message.php';
require __DIR__ . '/../app/models/Transfer.php';

use App\Websocket\ChatServer;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$database = new Database();
$db = null;
while ($db === null) {
    $db = $database->getConnection();
    if ($db === null) {
        echo "Database not ready, waiting...\n";
        sleep(2);
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer($db)
        )
    ),
    8080
);

echo "WebSocket server started on port 8080\n";

$server->run();