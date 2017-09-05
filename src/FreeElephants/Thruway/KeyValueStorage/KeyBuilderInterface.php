<?php


namespace FreeElephants\Thruway\KeyValueStorage;


interface KeyBuilderInterface
{

    public function buildKey(string $value): string;
}