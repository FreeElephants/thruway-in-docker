<?php

namespace FreeElephants\Thruway\WhitelistChecker;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class DummyListListChecker implements ListCheckerInterface
{

    public function exists(string $signature): bool
    {
        return true;
    }
}