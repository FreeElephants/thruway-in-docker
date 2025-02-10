<?php

namespace FreeElephants\Thruway\KeyValueStorage;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
interface KeyValueStorageInterface extends \ArrayAccess
{
    public function offsetExists(mixed $key): bool;
}
