<?php

namespace App\Core\Utility;


class DebugTimer
{

    private $start;
    private $end;

    public function start()
    {
        $this->start = microtime(true);
    }

    public function end()
    {
        $this->end = microtime(true);
    }

    public function print()
    {
        return ($this->end - $this->start) / 60;
    }
}
