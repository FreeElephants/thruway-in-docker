# Thruway (WAMP-router) in Docker

https://github.com/voryx/Thruway

## Run

Actual `$REVISION` value you can find at docker hub repository (see also in dev.env in sources). 

### As Single Service
```bash
docker run -d --name wamp-router \
    -e AUTHORIZATION_ENABLE=1 \
    -e AUTH_METHOD=jwt \
    -e JWT_SECRET_KEY=YOUR_SECRET_KEY \
    -e JWT_ALGO=HS256 \
    -e REALM=my_realm \
    -e ALLOW_REALM_AUTOCREATE=0 \
    -v $(pwd)/var/log/wamp:/var/log/thruway \
    -p 9000:9000 \
    freeelephants/thruway:${REVISION}
```

### With Docker Compose

```yaml
# docker-compose.yml
sevices:
    wamp-router:
      image: freeelephants/thruway:${REVISION} 
      volumes:
        - ./var/log/wamp:/var/log/thruway
      environment:
        - AUTHORIZATION_ENABLE=1
        - AUTH_METHOD=jwt
        - JWT_SECRET_KEY=${YOUR_SECRET_KEY}
        - JWT_ALGO=HS256
        - REALM=my_realm
        - ALLOW_REALM_AUTOCREATE=0
      ports:
        - 9000:9000
```

## Configure and Extends

### Environment Variables

See dev.env for actual default config and descriptions. 

### Components

For customize JWT logic you can mount `config/components-ext.php` file to containter. Use due implementations of `AbstractJwtDecoderFactory`, `JwtValidatorInterface`. 
 
See [php-di](https://github.com/FreeElephants/php-di) for more information. 

## Use with Redis key-value Storage as Backend for JWT Validation on Process Authenticate
  
Every time, on process authenticate, `JwtAuthenticationProvider` call injected `JwtValidator` instance. `JwtValidatorInterface` has one public method, for check that JWT signature is valid. 
For revoke JWT You can use black or white lists with hash JWT sums in system. 

Out of the box this image provide next Validators:
- `FreeElephants\Thruway\Validator\TrueDummyValidator` used by default. Already return true. 
- `FreeElephants\Thruway\Validator\WhitelistValidator`
- `FreeElephants\Thruway\Validator\BlacklistValidator`

`WhitelistValidator` and `BlacklistValidator` require `KeyValueStorageInterface` instance. See examples below.   

### Examples

#### Case 1: Use with Redis key-value Storage as Backend for Periodic Disconnect Clients by Black List
 
1. In some control panel you put it key-value storage: 
```php
<?php 
# Some AdminJwtController::revokeJWT()
# Value of `$user->getAuthId()` used in JWT field `authid'.
/**@var $redis \Redis*/
$redis->hSet('banned_in_wamp_auth_ids', $user->getAuthId(), time());
```

2. Configure router components: 
```php
<?php
# config/components-ext.php
$redis = new \Redis();
$redis->pconnect(REDIS_HOST, REDIS_PORT);
$redis->select(REDIS_DBINDEX);
$bannedInWampAuthStorage = new \FreeElephants\Thruway\KeyValueStorage\Redis\HashKeyStorageRedisAdapter($redis, 'banned_in_wamp_auth_ids');
return [
    'register' => [
    ],
    'instances' => [
        \Redis::class => $redis,
        \FreeElephants\Thruway\Timer\TimersList::class => new \FreeElephants\Thruway\Timer\TimersList([
            [10, new \FreeElephants\Thruway\Timer\AbortSessionsFromBlacklistTimer($bannedInWampAuthStorage)]
        ]),
    ],
];
```

3. Link Route with Redis
```yaml
# docker-compose.yml

services: 
    wamp-router:
      image: freeelephants/thruway:${REVISION}
      volumes:
        - ./var/log/wamp:/var/log/thruway
        - ./config/components-ext.php:/srv/thruway/config/componentns-ext.php
      environment:
        - AUTHORIZATION_ENABLE=1
        - AUTH_METHOD=jwt
        - JWT_SECRET_KEY=${YOUR_SECRET_KEY}
        - JWT_ALGO=HS256
        - REALM=my_realm
        - ALLOW_REALM_AUTOCREATE=0
        - REDIS_HOST=redis
        - REDIS_PORT=6379
        - REDIS_DBINDEX=1
      depends_on:
        - redis
    
    redis:
      image: redis
    
    backend:
      depends_on:
        - redis    

```

#### Case 2: Verify JWT by Black List on Open Connection 

1. In some control panel you put it key-value storage: 
```php
<?php 
# Some AdminJwtController::revokeJwtAction()
/**@var $redis \Redis*/
$redis->hSet('banned_in_wamp_auth_ids', $authId, time());
``` 

2. Configure router components:
```php
<?php
# config/components-ext.php
$redis = new \Redis();
$redis->pconnect(REDIS_HOST, REDIS_PORT);
$redis->select(REDIS_DBINDEX);

return [
    'register' => [
        \FreeElephants\Thruway\Jwt\JwtValidatorInterface::class => \FreeElephants\Thruway\Validator\BlacklistValidator::class
    ],
    'instances' => [
        \Redis::class => $redis,
        \FreeElephants\Thruway\KeyValueStorage\KeyValueStorageInterface::class => new \FreeElephants\Thruway\KeyValueStorage\Redis\HashKeyStorageRedisAdapter($redis, 'banned_in_wamp_auth_ids'),
    ],
];
```

3. Link Route with Redis
```yaml
# docker-compose.yml

services: 
    wamp-router:
      image: freeelephants/thruway:${REVISION} 
      volumes:
        - ./var/log/wamp:/var/log/thruway
        - ./config/components-ext.php:/srv/thruway/config/componentns-ext.php
      environment:
        - AUTHORIZATION_ENABLE=1
        - AUTH_METHOD=jwt
        - JWT_SECRET_KEY=${YOUR_SECRET_KEY}
        - JWT_ALGO=HS256
        - REALM=my_realm
        - ALLOW_REALM_AUTOCREATE=0
        - REDIS_HOST=redis
        - REDIS_PORT=6379
        - REDIS_DBINDEX=1
      depends_on:
        - redis
    
    redis:
      image: redis
    
    backend:
      depends_on:
        - redis    

```

## Contributing

### Installation (with docker)

```bash
make install 
```

### Testing
```bash
make test
```
