<?php

namespace FreeElephants\Thruway\Jwt;

abstract class AbstractJwtDecoderFactory
{

    abstract public function createJwtDecoderAdapter(string $key, string $algorithm): JwtDecoderAdapterInterface;
}
