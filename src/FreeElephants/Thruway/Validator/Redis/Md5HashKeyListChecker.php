<?php

namespace FreeElephants\Thruway\Validator\Redis;

use FreeElephants\Thruway\Validator\ListCheckerInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class Md5HashKeyListChecker implements ListCheckerInterface
{

    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var string
     */
    private $hashName;

    public function __construct(\Redis $redis, $hashName = REDIS_HASH_NAME)
    {
        $this->redis = $redis;
        $this->hashName = $hashName;
    }

    public function exists(string $signature): bool
    {
        return $this->redis->hExists($this->hashName, md5($signature));
    }
}