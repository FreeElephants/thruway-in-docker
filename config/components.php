<?php

use FreeElephants\Thruway\Jwt\AbstractJwtDecoderFactory;
use FreeElephants\Thruway\Jwt\FirebaseJwtDecoderFactory;
use FreeElephants\Thruway\Jwt\JwtValidatorInterface;
use FreeElephants\Thruway\Validator\TrueDummyValidator;
use Thruway\Peer\Router;
use Thruway\Peer\RouterInterface;

return [
    'register' => [
        RouterInterface::class => Router::class,
        AbstractJwtDecoderFactory::class => FirebaseJwtDecoderFactory::class,
        JwtValidatorInterface::class => TrueDummyValidator::class
    ],
];