<?php

namespace App\Infrastructure\Queue;

trait InteractsWithQueue
{
    /**
     * @var bool
     */
    protected $dispatchingNow = false;

    /**
     * Forces for dispatch now.
     */
    public function dispatchNow()
    {
        $this->dispatchingNow = true;
    }

    /**
     * @return bool
     */
    public function shouldQueue()
    {
        return !$this->dispatchingNow;
    }
}
