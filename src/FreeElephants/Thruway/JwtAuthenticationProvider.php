<?php

namespace FreeElephants\Thruway;

use FreeElephants\Thruway\Jwt\JwtDecoderAdapterInterface;
use FreeElephants\Thruway\Jwt\JwtValidatorInterface;
use FreeElephants\Thruway\Validator\ListCheckerInterface;
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
    /**
     * @var JwtValidatorInterface
     */
    private $validator;

    public function __construct(
        array $authRealms,
        JwtDecoderAdapterInterface $jwtDecoderAdapter,
        JwtValidatorInterface $validator
    ) {
        parent::__construct($authRealms);
        $this->jwtDecoderAdapter = $jwtDecoderAdapter;
        $this->validator = $validator;
    }

    public function getMethodName()
    {
        return 'jwt';
    }

    public function processAuthenticate($signature, $extra = null)
    {
        if ($this->validator->isValid($signature)) {
            $jwt = $this->jwtDecoderAdapter->decode($signature);
            if (isset($jwt->authid, $jwt->authroles) && is_array($jwt->authroles)) {
                return [
                    'SUCCESS',
                    [
                        'authid' => $jwt->authid,
                        'authroles' => $jwt->authroles
                    ]
                ];
            }
        }

        return ['FAILURE'];
    }
}
