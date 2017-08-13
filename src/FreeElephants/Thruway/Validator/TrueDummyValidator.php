<?php

namespace FreeElephants\Thruway\Validator;

use FreeElephants\Thruway\Jwt\JwtValidatorInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class TrueDummyValidator implements JwtValidatorInterface
{

    public function isValid(string $signature): bool
    {
        return true;
    }
}