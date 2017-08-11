<?php


namespace FreeElephants\Thruway\Jwt;


use Firebase\JWT\JWT;
use FreeElephants\Thruway\Jwt\Exception\InvalidArgumentException;
use FreeElephants\Thruway\Jwt\Exception\OutOfBoundsException;

class FirebaseJwtDecoderAdapter implements JwtDecoderAdapterInterface
{

    private $algorithms;
    /**
     * @var string
     */
    private $key;

    public function __construct(string $key, array $algorithms)
    {
        $this->key = $key;
        $this->setAlgorithms($algorithms);
    }

    public function getAlgorithms(): array
    {
        return $this->algorithms;
    }

    public function setAlgorithms(array $algorithms)
    {
        if (empty($algorithms)) {
            throw new InvalidArgumentException('JWT algorithms list can not be empty');
        }

        if ($outOfSupported = array_diff($algorithms, $this->getSupportedAlgorithms())) {
            throw new OutOfBoundsException('Some algorithms out of supported in this adapter: ' . implode($outOfSupported));
        }

        $this->algorithms = $algorithms;
    }

    public function decode(string $signature): \stdClass
    {
        return JWT::decode($signature, $this->key, $this->algorithms);
    }

    public function getSupportedAlgorithms(): array
    {
        return [
            'HS256',
            'HS384',
            'HS512',
            'RS256'
        ];
    }
}