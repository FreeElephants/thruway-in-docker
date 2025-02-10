<?php


namespace FreeElephants\Thruway\Jwt;


use FreeElephants\Thruway\Jwt\Exception\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class FirebaseJwtAdapterTest extends TestCase
{

    public function testDecode()
    {
        $decoder = new FirebaseJwtDecoderAdapter('example_key', 'HS256');

        $signature = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdXRoaWQiOiJqb2UiLCJhdXRocm9sZXMiOlsic3Vic2NyaWJlciJdfQ.IhmSdzXm8fzSN04XYoNx3CHdYhPJC9ujta_7R6ubQ-w';
        $expected = new \stdClass();
        $expected->authid = 'joe';
        $expected->authroles = [
            'subscriber'
        ];

        $this->assertEquals($expected, $decoder->decode($signature));
    }

    public function testSetAllowedAlgorithmsOutOfBoundsException()
    {
        $this->expectException(OutOfBoundsException::class);
        new FirebaseJwtDecoderAdapter('example_key', 'foo bar');
    }
}
