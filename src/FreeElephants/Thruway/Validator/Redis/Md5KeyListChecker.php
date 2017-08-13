<?php

namespace FreeElephants\Thruway\Validator\Redis;

use FreeElephants\Thruway\Validator\ListCheckerInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class Md5KeyListChecker implements ListCheckerInterface
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