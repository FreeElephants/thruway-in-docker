<?php

namespace FreeElephants\Thruway\WhitelistChecker;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
interface WhitelistCheckerInterface
{
    public function exists(string $signature): bool;
}