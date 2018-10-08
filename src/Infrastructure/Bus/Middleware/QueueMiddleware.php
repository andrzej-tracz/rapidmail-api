<?php

namespace App\Infrastructure\Bus\Middleware;

use App\Infrastructure\Queue\ShouldQueue;
use App\Infrastructure\Queue\Worker\CommandWorker;
use League\Tactician\Middleware;

class QueueMiddleware implements Middleware
{
    /**
     * @var CommandWorker
     */
    protected $worker;

    public function __construct(CommandWorker $worker)
    {
        $this->worker = $worker;
    }

    /**
     * @param object   $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if ($command instanceof ShouldQueue && $command->shouldQueue()) {
            return $this->queueCommand($command);
        }

        return $next($command);
    }

    protected function queueCommand($command)
    {
        $this->worker->later()->handle($command);
    }
}
