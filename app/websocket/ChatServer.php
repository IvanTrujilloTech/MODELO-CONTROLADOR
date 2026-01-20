<?php

namespace App\Websocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ChatServer implements MessageComponentInterface {
    protected $clients;
    protected $db;
    protected $message;

    public function __construct($db) {
        $this->clients = new \SplObjectStorage;
        $this->db = $db;
        require_once __DIR__ . '/../models/Message.php';
        $this->message = new \Message($this->db);
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if ($data && isset($data['user_id']) && isset($data['message'])) {
            // Save message
            $this->message->sender_id = $data['user_id'];
            $this->message->receiver_id = null; // Global chat
            $this->message->message = $data['message'];
            $this->message->create();
        }

        $numRecv = count($this->clients);
        echo sprintf('Connection %d sending message "%s" to %d connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            // Send to all clients including the sender
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}