<?php

use FreeElephants\Thruway\Jwt\AbstractJwtDecoderFactory;
use FreeElephants\Thruway\Jwt\FirebaseJwtDecoderFactory;
use FreeElephants\Thruway\Jwt\JwtValidatorInterface;
use FreeElephants\Thruway\Timer\TimersList;
use FreeElephants\Thruway\Validator\TrueDummyValidator;
use React\EventLoop\LoopInterface;
use React\EventLoop\StreamSelectLoop;
use Thruway\Peer\Router;
use Thruway\Peer\RouterInterface;

return [
    \FreeElephants\DI\InjectorBuilder::REGISTER_KEY => [
        // In theory you can use another implementation of LoopInterface, but now, by fact only native StreamSelectLoop supported in PHP7
        LoopInterface::class => StreamSelectLoop::class,
        // You can extend Thruway\Router and override via interface in ext-components.php
        RouterInterface::class => Router::class,
        AbstractJwtDecoderFactory::class => FirebaseJwtDecoderFactory::class,
        JwtValidatorInterface::class => TrueDummyValidator::class,
        TimersList::class => TimersList::class,
    ],
    \FreeElephants\DI\InjectorBuilder::INSTANCES_KEY => [

    ],
];
