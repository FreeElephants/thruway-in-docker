<?php

namespace FreeElephants\Thruway\Validator;

use FreeElephants\Thruway\Jwt\JwtValidatorInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class BlacklistValidator implements JwtValidatorInterface
{

    /**
     * @var ListCheckerInterface
     */
    private $listChecker;

    public function __construct(ListCheckerInterface $listChecker)
    {
        $this->listChecker = $listChecker;
    }

    public function isValid(string $signature): bool
    {
        return !$this->listChecker->exists($signature);
    }
}