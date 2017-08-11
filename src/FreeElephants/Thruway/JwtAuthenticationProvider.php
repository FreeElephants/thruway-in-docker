<?php

namespace FreeElephants\Thruway;

use FreeElephants\Thruway\Exception\InvalidArgumentException;
use FreeElephants\Thruway\Jwt\JwtDecoderAdapterInterface;
use Thruway\Authentication\AbstractAuthProviderClient;

/**
 * @author samizdam <samizdam@inbox.ru>
 * Code based on example https://github.com/mbonneau/ThruwayJwtAuthExample/blob/master/JwtAuthenticationProvider.php
 */
class JwtAuthenticationProvider extends AbstractAuthProviderClient
{
    /**
     * @var JwtDecoderAdapterInterface
     */
    private $jwtDecoderAdapter;

    public function __construct(array $authRealms, JwtDecoderAdapterInterface $jwtDecoderAdapter)
    {
        parent::__construct($authRealms);
        $this->jwtDecoderAdapter = $jwtDecoderAdapter;
    }

    public function getMethodName()
    {
        return 'jwt';
    }

    public function processAuthenticate($signature, $extra = null)
    {
        $jwt = $this->jwtDecoderAdapter->decode($signature);
        if (isset($jwt->authid, $jwt->authroles) && is_array($jwt->authroles)) {
            return [
                'SUCCESS',
                [
                    'authid' => $jwt->authid,
                    'authroles' => $jwt->authroles
                ]
            ];
        } else {
            return ['FAILURE'];
        }
    }
}
