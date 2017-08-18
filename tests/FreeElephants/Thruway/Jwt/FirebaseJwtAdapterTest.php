<?php


namespace FreeElephants\Thruway\Jwt;


use FreeElephants\Thruway\Jwt\Exception\InvalidArgumentException;
use FreeElephants\Thruway\Jwt\Exception\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class FirebaseJwtAdapterTest extends TestCase
{

    public function testDecode()
    {
        $decoder = new FirebaseJwtDecoderAdapter('example_key', ['HS256', 'HS384']);

        $signature = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdXRoaWQiOiJqb2UiLCJhdXRocm9sZXMiOlsic3Vic2NyaWJlciJdfQ.Lxyy1H3gfs1FV5UJLGxfAYvS1TJeiJhVInu5GIlccg4';
        $expected = new \stdClass();
        $expected->authid = 'joe';
        $expected->authroles = [
            'subscriber'
        ];

        $this->assertEquals($expected, $decoder->decode($signature));
    }

    public function testUseEmptyAllowedAlgorithmsListInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        new FirebaseJwtDecoderAdapter('example_key', []);
    }

    public function testSetAllowedAlgorithmsOutOfBoundsException()
    {
        $this->expectException(OutOfBoundsException::class);
        new FirebaseJwtDecoderAdapter('example_key', ['foo bar']);
    }
}