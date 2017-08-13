<?php

namespace FreeElephants\Thruway\Jwt;

use FreeElephants\DI\Injector;

abstract class AbstractJwtDecoderFactory
{

    /**
     * @var Injector
     */
    private $di;

    public function __construct(Injector $di)
    {
        $this->di = $di;
    }

    abstract public function createJwtDecoderAdapter(string $key, array $allowedAlgorithms): JwtDecoderAdapterInterface;
}