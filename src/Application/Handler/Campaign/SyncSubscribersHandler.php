<?php

namespace App\Application\Handler\Campaign;

use App\Application\Command\Campaign\SyncSubscribersCommand;
use App\Infrastructure\Campaign\Repository\MessageRepository;

class SyncSubscribersHandler
{
    /**
     * @var MessageRepository
     */
    protected $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(SyncSubscribersCommand $command)
    {
        $campaign = $command->campaign();

        $this->repository->removePendingOrQueued($campaign);

        $this->repository->createFromSubscribersList($campaign);
    }
}
