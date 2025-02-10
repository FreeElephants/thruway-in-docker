<?php


namespace FreeElephants\Thruway\Jwt;


interface JwtDecoderAdapterInterface
{

    public function getAlgorithm(): string;

    public function setAlgorithm(string $algorithm);

    public function decode(string $signature): \stdClass;

    public function getSupportedAlgorithms(): array;
}
