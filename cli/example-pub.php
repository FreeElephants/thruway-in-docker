<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Thruway\ClientSession;
use Thruway\Peer\Client;
use Thruway\Transport\PawlTransportProvider;

$client = new Client(getenv('REALM'));
$client->addTransportProvider(new PawlTransportProvider("ws://wamp-router:9000/"));

$client->on('open', function (ClientSession $session) {
    // 2) publish an event
    $session->publish('com.myapp.hello', ['Hello, world from PHP!!!'], [], ["acknowledge" => true])->then(
        function () {
            echo "Publish Acknowledged!\n";
        },
        function ($error) {
            // publish failed
            echo "Publish Error {$error}\n";
        }
    );
});

$client->start();
