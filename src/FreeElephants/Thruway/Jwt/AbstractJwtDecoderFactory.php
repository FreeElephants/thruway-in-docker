<?php

namespace FreeElephants\Thruway\Jwt;

abstract class AbstractJwtDecoderFactory
{

    abstract public function createJwtDecoderAdapter(string $key, array $allowedAlgorithms): JwtDecoderAdapterInterface;
}