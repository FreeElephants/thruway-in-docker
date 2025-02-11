<?php

namespace FreeElephants\Thruway\KeyValueStorage\Redis;

use FreeElephants\Thruway\KeyValueStorage\KeyValueStorageInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class HashKeyStorageRedisAdapter implements KeyValueStorageInterface
{

    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var string
     */
    private $hashName;

    public function __construct(\Redis $redis, string $hashName)
    {
        $this->redis = $redis;
        $this->hashName = $hashName;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->redis->hExists($this->hashName, $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->redis->hGet($this->hashName, $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->redis->hSet($this->hashName, $offset, $value);
    }

        public function offsetUnset(mixed $offset): void
    {
        $this->redis->hDel($this->hashName, $offset);
    }
}
