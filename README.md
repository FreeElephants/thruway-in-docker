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
        - JWT_ALGO=HS256
        - REALM=my_realm
        - ALLOW_REALM_AUTOCREATE=0
      ports:
        - 8080:9000
```

## Configure and Extends

For customize JWT logic you can mount `config/ext-components.php` file to containter. Use due implementations of `AbstractJwtDecoderFactory`, `JwtValidatorInterface`. 
 
See [php-di](https://github.com/FreeElephants/php-di) for more information. 

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