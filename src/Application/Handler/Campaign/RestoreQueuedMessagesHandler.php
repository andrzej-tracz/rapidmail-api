<?php

namespace App\Application\Handler\Campaign;

use App\Application\Command\Campaign\RestoreQueuedMessagesCommand;
use App\Infrastructure\Campaign\Repository\MessageRepository;

class RestoreQueuedMessagesHandler
{
    /**
     * @var MessageRepository
     */
    private $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handles command.
     *
     * @param RestoreQueuedMessagesCommand $command
     */
    public function handle(RestoreQueuedMessagesCommand $command)
    {
        $campaign = $command->campaign();

        $this->repository->restorePausedMessages($campaign);
    }
}
