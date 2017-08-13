<?php

namespace FreeElephants\Thruway\Jwt;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
interface JwtValidatorInterface
{

    public function isValid(string $signature): bool;
}