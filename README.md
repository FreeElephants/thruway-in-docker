# Thruway (WAMP-router) in Docker

## Run 

### As Single Service
```bash
docker run -d --name wamp-router \
    -e JWT_SECRET_KEY=YOUR_SECRET_KEY \
    -e JWT_ALGO=HS256 \
    -e REALM=my_realm \
    -e ALLOW_REALM_AUTOCREATE=0 \
    -v $(pwd)/var/log/:/var/log/ \
    -p 8080:9000 \
    freeelephants/thruway:1.0.0
```

### With Docker Compose

```yaml
# docker-compose.yml
sevices:
    wamp-router:
      image: freeelephants/thruway:1.0.0 
      volumes:
        - ./var/log/:/var/log/wamp
      environment:
        - JWT_SECRET_KEY=${YOUR_SECRET_KEY}
        - JWT_ALGOS=HS256
        - REALM=my_realm
        - ALLOW_REALM_AUTOCREATE=0
      ports:
        - 8080:9000
```

## Configure and Extends

### Environment Variables
- `JWT_SECRET_KEY` -- key for decode JWT, required.
- `JWT_ALGOS` -- comma separated list of allowed algorithms, default value `HS256`.
- `REALM` -- name of realm, required. 
- `ALLOW_REALM_AUTOCREATE` -- allow clients to create realms on router, default false (0). 
- `REDIS_HOST` -- host name for connection to Redis, optional, default `redis`. Need if you use Redis, see validation section bellow. 
- `REDIS_PORT`-- port number for connection to Redis, default `6379`. 
- `REDIS_DBINDEX` -- number of Redis db for select it, default `1`.  
- `REDIS_HASH_NAME` -- name of hash in Redis.  

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

`WhitelistValidator` and `BlacklistValidator` require `ListCheckerInterface` instance. You can use `FreeElephants\Thruway\Validator\Redis\Md5HashKeyListChecker` or `FreeElephants\Thruway\Validator\Redis\Md5KeyListChecker`. 

### Example

_Case: You want revoke JWT by black list._ 

1. In some control panel you put it key-value storage: 
```php
<?php 
# Some AdminJwtController::revokeJWT()
/**@var $redis \Redis*/
$redis->hSet('revoked_jwt', md5($jwtString), time());
``` 

2. Configure router services:
```php
<?php
# config/components-ext.php
$redis = new \Redis();
$redis->pconnect(REDIS_HOST, REDIS_PORT);
$redis->select(REDIS_DBINDEX);

return [
    'register' => [
        \FreeElephants\Thruway\Validator\ListCheckerInterface::class => \FreeElephants\Thruway\Validator\Redis\Md5HashKeyListChecker::class,
        \FreeElephants\Thruway\Jwt\JwtValidatorInterface::class => \FreeElephants\Thruway\Validator\BlacklistValidator::class
    ],
    'instances' => [
        \Redis::class => $redis,
    ],
];
```

3. Link Route with Redis
```yaml
# docker-compose.yml

services: 
    wamp-router:
      image: freeelephants/thruway:1.0.0 
      volumes:
        - ./var/log/wamp:/var/log
        - ./config/components-ext.php:/srv/thruway/config/componentns-ext.php
      environment:
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
      image: redis:2.8.19
    
    backend:
      depends_on:
        - redis    

```


## Use with Redis key-value Storage as Backend for JWT Validation for Periodic Disconnect Clients by Black List
...TBD...

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
docker build . -t freeelephants/thruway:1.0.0 
```