<?php


namespace FreeElephants\Thruway\Jwt;


class FirebaseJwtDecoderFactory extends AbstractJwtDecoderFactory
{

    public function createJwtDecoderAdapter(string $key, string $algorithm): JwtDecoderAdapterInterface
    {
        return new FirebaseJwtDecoderAdapter($key, $algorithm);
    }
}
