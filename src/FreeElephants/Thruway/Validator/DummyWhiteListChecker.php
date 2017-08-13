<?php

namespace FreeElephants\Thruway\WhitelistChecker;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class DummyWhiteListChecker implements WhitelistCheckerInterface
{

    public function exists(string $signature): bool
    {
        return true;
    }
}