<?php

namespace FreeElephants\Thruway\Validator;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
interface WhitelistCheckerInterface
{
    public function exists(string $signature): bool;
}