<?php

namespace FreeElephants\Thruway\WhitelistChecker;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class Md5HashKeyInRedisChecker implements WhitelistCheckerInterface
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

    public function exists(string $signature): bool
    {
        return $this->redis->hExists($this->hashName, md5($signature));
    }
}