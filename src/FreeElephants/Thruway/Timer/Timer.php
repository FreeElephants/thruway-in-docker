<?php

namespace FreeElephants\Thruway\Timer;

use Thruway\Realm;

interface Timer
{

    public function execute(Realm $realm): void;
}