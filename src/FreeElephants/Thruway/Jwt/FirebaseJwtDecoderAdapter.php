<?php


namespace FreeElephants\Thruway\Jwt;


use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use FreeElephants\Thruway\Jwt\Exception\OutOfBoundsException;

class FirebaseJwtDecoderAdapter implements JwtDecoderAdapterInterface
{

    private string $algorithm;
    private string $key;

    public function __construct(string $key, string $algorithm)
    {
        $this->key = $key;
        $this->setAlgorithm($algorithm);
    }

    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(string $algorithm)
    {
        if (!in_array($algorithm, $this->getSupportedAlgorithms())) {
            throw new OutOfBoundsException('Algorithm out of supported in this adapter: ' . implode($this->getSupportedAlgorithms()));
        }

        $this->algorithm = $algorithm;
    }

    public function decode(string $signature): \stdClass
    {
        return JWT::decode($signature, new Key($this->key, $this->algorithm));
    }

    public function getSupportedAlgorithms(): array
    {
        return [
            'HS256',
            'HS384',
            'HS512',
            'RS256',
        ];
    }
}
