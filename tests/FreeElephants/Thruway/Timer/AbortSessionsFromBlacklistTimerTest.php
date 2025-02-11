<?php
declare(strict_types=1);

namespace FreeElephants\Thruway\Timer;

use FreeElephants\Thruway\KeyValueStorage\KeyValueStorageInterface;
use PHPUnit\Framework\TestCase;
use Thruway\Authentication\AuthenticationDetails;
use Thruway\Realm;
use Thruway\Session;

class AbortSessionsFromBlacklistTimerTest extends TestCase
{

    public function testExecute(): void
    {
        $storage = new class extends \ArrayObject implements KeyValueStorageInterface{};
        $storage->offsetSet('foo', true);
        $timer = new AbortSessionsFromBlacklistTimer($storage);
        $realm = $this->createMock(Realm::class);
        $session = $this->createMock(Session::class);
        $authenticationDetails = new AuthenticationDetails();
        $authenticationDetails->setAuthId('foo');
        $session->method('getAuthenticationDetails')->willReturn($authenticationDetails);

        $realm->method('getSessions')->willReturn([$session]);

        $session->expects($this->once())->method('shutdown');
        $session->expects($this->once())->method('setAuthenticated')->with(false);

        $timer->execute($realm);
    }
}
