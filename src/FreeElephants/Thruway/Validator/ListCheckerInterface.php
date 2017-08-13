<?php

namespace FreeElephants\Thruway\Validator;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
interface ListCheckerInterface
{
    public function exists(string $signature): bool;
}