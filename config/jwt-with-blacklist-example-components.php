<?php
$redis = new \Redis();
$redis->pconnect(REDIS_HOST, REDIS_PORT);
$redis->select(REDIS_DBINDEX);
$bannedInWampAuthStorage = new \FreeElephants\Thruway\KeyValueStorage\Redis\HashKeyStorageRedisAdapter($redis, 'banned_in_wamp_auth_ids');

return [
    'register' => [
        \FreeElephants\Thruway\Jwt\JwtValidatorInterface::class => \FreeElephants\Thruway\Validator\BlacklistValidator::class
    ],
    'instances' => [
        \Redis::class => $redis,
        \FreeElephants\Thruway\KeyValueStorage\KeyValueStorageInterface::class => $bannedInWampAuthStorage,
        \FreeElephants\Thruway\Timer\TimersList::class => new \FreeElephants\Thruway\Timer\TimersList([
            [10, new \FreeElephants\Thruway\Timer\AbortSessionsFromBlacklistTimer($bannedInWampAuthStorage)]
        ]),
    ],
];