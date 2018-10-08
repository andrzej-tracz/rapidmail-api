<?php

namespace App\Application\Handler\Campaign;

use App\Application\Command\Campaign\SendMessageCommand;
use App\Application\Command\Campaign\SendMessagesCommand;
use App\Domain\Campaign\Message;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\Campaign\Repository\CampaignRepository;
use App\Infrastructure\Campaign\Repository\MessageRepository;
use App\Infrastructure\Utils\Str;
use Psr\Log\LoggerInterface;

class SendMessagesHandler
{
    /**
     * @var MessageRepository
     */
    protected $repository;

    /**
     * @var CampaignRepository
     */
    protected $campaignRepository;

    /**
     * @var CommandBusInterface
     */
    protected $bus;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        MessageRepository $repository,
        CampaignRepository $campaignRepository,
        CommandBusInterface $bus,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->campaignRepository = $campaignRepository;
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function handle(SendMessagesCommand $command)
    {
        $campaign = $command->campaign();

        /** @var $messages Message[] */
        $messages = $this->repository->getPending($campaign);

        if (null === $campaign->getSendingStartsAt()) {
            $campaign->setSendingStartsAt(new \DateTime());
            $this->campaignRepository->save($campaign);

            $this->logger->info("Mark campaign as started: {$campaign->getId()}");
        }

        foreach ($messages as $message) {
            $message->setStatus(Message::STATUS_QUEUED);
            $message->setToken(Str::random(100));
            $this->repository->save($message);

            $this->logger->info("Mark message as queued: {$message->getId()}");

            $this->bus->handle(
                new SendMessageCommand($message)
            );
        }

        if ($this->repository->getPendingMessagesCount($campaign) > 0) {
            $this->bus->handle(
                new SendMessagesCommand($campaign)
            );
        }
    }
}
