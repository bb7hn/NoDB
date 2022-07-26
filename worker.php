<?php
use Workerman\Worker;
require_once './vendor/autoload.php';

// Create A Worker and Listens 3000 port, use Websocket protocol
$worker = new Worker("websocket://127.0.0.3:3000");

// 1 process
$worker->count = 1;

// Emitted when new connection come
$worker->onConnect = function($connection){
    // Emitted when websocket handshake done
    $connection->onWebSocketConnect = function($connection,$request){
        echo "New connection.\nipAddres => " . $connection->getRemoteIp() . "\n";
        echo"\n\n";
    };
    $connection->onMessage = function($connection, $d){
        $data = json_decode($d);
        if(isset($data->method) && isset($data->data)){
            $connection->send('received successfully.');
            var_dump($data->data);
        }
        else{
            $connection->send('invalid request!');
        }
        
    };
};


// Emitted when connection closed
$worker->onClose = function($connection){
    echo "Connection closed";
};

// Run worker
Worker::runAll();