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
    /* $connection->onWebSocketConnect = function($connection,$request){
        echo "New connection.\nipAddres => " . $connection->getRemoteIp() . "\n";
        echo"\n\n";
    }; */
    $connection->onMessage = function($connection, $d){
        echo "\n\n";
        global $validMethods;
        $data = json_decode($d);
        if(isset($data->method) && isset($data->data)){
            $connection->send('received successfully.');
                switch ($data->method) {
                    case 'create':
                        echo "new create request";
                        break;
                    case 'insert':
                        echo "new insert request";
                        break;
                    case 'update':
                        echo "new update request";
                        break;
                    case 'get':
                        echo "invalid request:";
                        break;
                    default:
                        echo "invalid request: ";
                        $connection->send('invalid request!');
                        break;
                }
        }
        else{
            $connection->send('invalid request!');
        }
        $connection->close('connection closed');
    };
};


// Emitted when connection closed
$worker->onClose = function($connection){
    echo "\nConnection closed";
};

// Run worker
Worker::runAll();
