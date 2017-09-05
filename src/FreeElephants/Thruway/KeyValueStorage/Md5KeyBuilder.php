<?php


namespace FreeElephants\Thruway\KeyValueStorage;


use FreeElephants\Thruway\KeyValueStorage\KeyBuilderInterface;

class Md5KeyBuilder implements KeyBuilderInterface
{

    public function buildKey(string $value): string
    {
        return md5($value);
    }
}