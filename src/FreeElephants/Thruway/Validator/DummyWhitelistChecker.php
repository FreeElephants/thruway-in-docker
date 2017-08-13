<?php

namespace FreeElephants\Thruway\Checker;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class DummyWhitelistChecker implements ListCheckerInterface
{

    public function exists(string $signature): bool
    {
        return true;
    }
}