<?php

namespace App\Infrastructure\Queue;

interface ShouldQueue
{
    /**
     * @return mixed
     */
    public function dispatchNow();

    /**
     * @return bool
     */
    public function shouldQueue();
}
