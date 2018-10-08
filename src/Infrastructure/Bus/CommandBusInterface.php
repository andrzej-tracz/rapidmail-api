<?php

namespace App\Infrastructure\Bus;

interface CommandBusInterface
{
    /**
     * Send given command through middlewares pipelines.
     *
     * @param $command
     *
     * @return mixed
     */
    public function handle($command);
}
