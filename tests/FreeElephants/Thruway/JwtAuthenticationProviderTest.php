<?php
declare(strict_types=1);

namespace FreeElephants\Thruway;

use FreeElephants\Thruway\Jwt\JwtDecoderAdapterInterface;
use FreeElephants\Thruway\Jwt\JwtValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Thruway\Logging\Logger;

class JwtAuthenticationProviderTest extends TestCase
{
    public function testProcessAuthenticateFailure(): void
    {
        Logger::set(new NullLogger());
        $provider = new JwtAuthenticationProvider(
            [],
            $this->createMock(JwtDecoderAdapterInterface::class),
            $this->createMock(JwtValidatorInterface::class),
        );

        $result = $provider->processAuthenticate('foo');

        $this->assertSame(['FAILURE'], $result);
    }

    public function testProcessAuthenticateSuccess(): void
    {
        Logger::set(new NullLogger());
        $jwtValidator = $this->createMock(JwtValidatorInterface::class);
        $jwtValidator->method('isValid')->willReturn(true);
        $jwtDecoder = $this->createMock(JwtDecoderAdapterInterface::class);
        $jwtDecoder->method('decode')->willReturn((object) ['bar' => 'baz']);
        $provider = new JwtAuthenticationProvider(
            [],
            $jwtDecoder,
            $jwtValidator,
        );

        $result = $provider->processAuthenticate('foo');

        $this->assertSame(['SUCCESS', [
            'bar' => 'baz',
        ]], $result);
    }
}
