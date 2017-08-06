<?php

namespace FreeElephants\Thruway;

use Thruway\Authentication\AbstractAuthProviderClient;

/**
 * @author samizdam <samizdam@inbox.ru>
 * Code based on example https://github.com/mbonneau/ThruwayJwtAuthExample/blob/master/JwtAuthenticationProvider.php
 */
class JwtAuthenticationProvider extends AbstractAuthProviderClient
{
    private $jwtKey;

    public function __construct(array $authRealms, $jwtKey)
    {
        $this->jwtKey = $jwtKey;
        parent::__construct($authRealms);
    }

    public function getMethodName()
    {
        return 'jwt';
    }

    public function processAuthenticate($signature, $extra = null)
    {
        $jwt = \Firebase\JWT\JWT::decode($signature, $this->jwtKey, ['JWT_ALGO']);
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
