# Thruway (WAMP-router) in Docker

## Run 
docker run -d --name wamp-router \
    -e JWT_SECRET_KEY=YOUR_SECRET_KEY \
    -e JWT_ALGO=HS256 \
    -e REALM=my_realm \
    -e ALLOW_REALM_AUTOCREATE=0 \
    -v $(pwd)/var/log/:/var/log/ \
    -p 8080:9000 \
    freeelephants/thruway:1.0.0



## Contributing

### Installation

```bash
./tools/composer.sh install 
```

### Build
```bash
./tools/composer.sh install --no-dev --prefer-dist --ignore-platform-reqs
docker build . -t freeelephants/thruway:1.0.0 
```