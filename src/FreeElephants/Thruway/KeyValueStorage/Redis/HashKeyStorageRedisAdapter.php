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

    public function offsetExists($offset)
    {
        return $this->redis->hExists($this->hashName, $offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->redis->hGet($this->hashName, $offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->redis->hSet($this->hashName, $offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->redis->hDel($this->hashName, $offset);
    }
}