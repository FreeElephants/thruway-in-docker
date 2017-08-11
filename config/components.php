<?php


use FreeElephants\Thruway\Jwt\AbstractJwtDecoderFactory;
use FreeElephants\Thruway\Jwt\FirebaseJwtDecoderAdapter;
use Thruway\Peer\Router;
use Thruway\Peer\RouterInterface;

return [
    'register' => [
        RouterInterface::class => Router::class,
        AbstractJwtDecoderFactory::class => FirebaseJwtDecoderAdapter::class
    ],
];