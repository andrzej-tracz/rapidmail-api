<?php

namespace App\Infrastructure\Queue\Worker;

use App\Infrastructure\Queue\Bus\QueuedCommandBus;
use App\Infrastructure\Queue\CommandRefresher;
use App\Infrastructure\Queue\ShouldQueue;
use Psr\Container\ContainerInterface;

class CommandWorker extends \Dtc\QueueBundle\Model\Worker
{
    /**
     * @var ContainerInterface
     */
    protected $bus;

    /**
     * @var CommandRefresher
     */
    protected $refresher;

    public function __construct(QueuedCommandBus $bus, CommandRefresher $refresher)
    {
        $this->bus = $bus;
        $this->refresher = $refresher;
    }

    /**
     * Handles queued command.
     *
     * @param ShouldQueue $command
     *
     * @throws \Exception
     */
    public function handle(ShouldQueue $command)
    {
        $command = $this->refresher->refreshCommand($command);

        $command->dispatchNow();

        $this->bus->handle($command);
    }

    public function getName()
    {
        return 'command_worker';
    }
}
