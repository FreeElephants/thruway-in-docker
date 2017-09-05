<?php

namespace FreeElephants\Thruway\Timer;

use FreeElephants\Thruway\KeyValueStorage\KeyValueStorageInterface;
use Thruway\Realm;
use Thruway\Session;

class AbortSessionsFromBlacklistTimer implements Timer
{
    /**
     * @var KeyValueStorageInterface
     */
    private $storage;

    public function __construct(KeyValueStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function execute(Realm $realm): void
    {
        $sessions = $realm->getSessions();
        /**@var $session Session */
        foreach ($sessions as $session) {
            $authId = $session->getAuthenticationDetails()->getAuthId();
            if ($this->storage->offsetExists($authId)) {
                $session->setAuthenticated(false);
                $session->shutdown();
            }
        }
    }
}