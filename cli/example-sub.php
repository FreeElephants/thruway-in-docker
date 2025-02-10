<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Thruway\ClientSession;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;

$client = new Client(getenv('REALM'));
$client->addTransportProvider(new PawlTransportProvider("ws://wamp-router:9000/"));

$client->on('open', function (ClientSession $session) {

    // 1) subscribe to a topic
    $onevent = function ($args) {
        echo "Event {$args[0]}\n";
    };

    $session->subscribe('com.myapp.hello', $onevent);
});

$client->start();
