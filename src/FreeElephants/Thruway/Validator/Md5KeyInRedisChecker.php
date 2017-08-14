<?php

namespace FreeElephants\Thruway\Validator;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class Md5KeyInRedisChecker implements WhitelistCheckerInterface
{

    /**
     * @var \Redis
     */
    private $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function exists(string $signature): bool
    {
        return $this->redis->exists(md5($signature));
    }
}