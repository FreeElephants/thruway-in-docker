<?php

namespace FreeElephants\Thruway\Timer;

class TimersList
{


    private $timers;

    /**
     * VisitorList constructor.
     * @param array $timersConfig like [[10, new SomeTimes()], [15, new AnotherTimer()]]
     */
    public function __construct(array $timersConfig = [])
    {
        $this->timers = new \SplObjectStorage();
        foreach ($timersConfig as $item) {
            $interval = $item[0];
            $visitor = $item[1];
            $this->timers->attach($visitor, $interval);
        }
    }

    /**
     * @return \SplObjectStorage|Timer where keys is visitors instances, and attached data is interval for timer.
     */
    public function getTimers(): \SplObjectStorage
    {
        return $this->timers;
    }
}
