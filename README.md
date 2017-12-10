# Thruway (WAMP-router) in Docker

## Run 

### As Single Service
```bash
docker run -d --name wamp-router \
    -e AUTHORIZATION_ENABLE=1 \
    -e AUTH_METHOD=jwt \
    -e JWT_SECRET_KEY=YOUR_SECRET_KEY \
    -e JWT_ALGOS=HS256 \
    -e REALM=my_realm \
    -e ALLOW_REALM_AUTOCREATE=0 \
    -v $(pwd)/var/log/wamp:/var/log/thruway \
    -p 8080:9000 \
    freeelephants/thruway:0.3.0
```

### With Docker Compose

```yaml
# docker-compose.yml
sevices:
    wamp-router:
      image: freeelephants/thruway:0.3.0 
      volumes:
        - ./var/log/wamp:/var/log/thruway
      environment:
        - AUTHORIZATION_ENABLE=1
        - AUTH_METHOD=jwt
        - JWT_SECRET_KEY=${YOUR_SECRET_KEY}
        - JWT_ALGOS=HS256
        - REALM=my_realm
        - ALLOW_REALM_AUTOCREATE=0
      ports:
        - 8080:9000
```

## Configure and Extends

### Environment Variables
- `AUTHORIZATION_ENABLE` -- use Authorization manager. Default false (not set). 
- `AUTH_METHOD` -- method for authenticate, default not use (false). Supported values: `jwt`. 
- `JWT_SECRET_KEY` -- key for decode JWT, required.
- `JWT_ALGOS` -- comma separated list of allowed algorithms, default value `HS256`.
- `REALM` -- name of realm, required. 
- `ALLOW_REALM_AUTOCREATE` -- allow clients to create realms on router, default false (0).
- `THRUWAY_DEBUG_ENABLE` -- enable stdOut logging, default false (0).
- `REDIS_HOST` -- host name for connection to Redis, optional, default `redis`. Need if you use Redis, see validation section bellow. 
- `REDIS_PORT`-- port number for connection to Redis, default `6379`. 
- `REDIS_DBINDEX` -- number of Redis db for select it, default `1`.  

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
      image: freeelephants/thruway:0.3.0 
      volumes:
        - ./var/log/wamp:/var/log/thruway
        - ./config/components-ext.php:/srv/thruway/config/componentns-ext.php
      environment:
        - AUTHORIZATION_ENABLE=1
        - AUTH_METHOD=jwt
        - JWT_SECRET_KEY=${YOUR_SECRET_KEY}
        - JWT_ALGOS=HS256
        - REALM=my_realm
        - ALLOW_REALM_AUTOCREATE=0
        - REDIS_HOST=redis
        - REDIS_PORT=6379
        - REDIS_DBINDEX=1
      depends_on:
        - redis
    
    redis:
      image: redis:2.8.19
    
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
      image: freeelephants/thruway:0.3.0 
      volumes:
        - ./var/log/wamp:/var/log/thruway
        - ./config/components-ext.php:/srv/thruway/config/componentns-ext.php
      environment:
        - AUTHORIZATION_ENABLE=1
        - AUTH_METHOD=jwt
        - JWT_SECRET_KEY=${YOUR_SECRET_KEY}
        - JWT_ALGOS=HS256
        - REALM=my_realm
        - ALLOW_REALM_AUTOCREATE=0
        - REDIS_HOST=redis
        - REDIS_PORT=6379
        - REDIS_DBINDEX=1
      depends_on:
        - redis
    
    redis:
      image: redis:2.8.19
    
    backend:
      depends_on:
        - redis    

```

## FAQ:

Q: On connect client in my container with PawlTransportProvider I get error `Could not connect: DNS Request did not return valid answer.`.
A: You need resolve router container name to IP: 
```php
$url = sprintf('ws://%s:9000/', gethostbyname('wamp-router')); // your router container name
$client->addTransportProvider(new \Thruway\Transport\PawlTransportProvider($url);
```

Q: On connect client I get error `[Thruway\Transport\PawlTransportProvider 18] Received: [3,{},"wamp.error.not_authorized"]`
A: You need specify auth roles. See examples https://github.com/voryx/Thruway/issues/93 

## Contributing

### Installation

```bash
./tools/composer.sh install 
```

### Testing
```bash
vendor/bin/phpunit
```

### Build
```bash
./tools/composer.sh install --no-dev --prefer-dist --ignore-platform-reqs
docker build . -t freeelephants/thruway:0.3.0 
```