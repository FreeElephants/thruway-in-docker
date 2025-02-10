<?php

namespace FreeElephants\Thruway\KeyValueStorage\Redis;

use FreeElephants\Thruway\KeyValueStorage\KeyValueStorageInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class KeyStorageRedisAdapter implements KeyValueStorageInterface
{
    public function __construct(private readonly \Redis $redis)
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->redis->exists($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->redis->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value):void
    {
        $this->redis->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->redis->del($offset);
    }
}
