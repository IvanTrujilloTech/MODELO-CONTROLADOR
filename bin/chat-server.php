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
$attempts = 0;
$maxAttempts = 30;

echo "Attempting to connect to database...\n";

while ($db === null && $attempts < $maxAttempts) {
    $db = $database->getConnection();
    $attempts++;
    if ($db === null) {
        echo "Database not ready, waiting 2 seconds... (Attempt $attempts/$maxAttempts)\n";
        sleep(2);
    }
}

if ($db === null) {
    echo "Failed to connect to database after $maxAttempts attempts. Exiting...\n";
    exit(1);
}

echo "Database connection successful!\n";

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
