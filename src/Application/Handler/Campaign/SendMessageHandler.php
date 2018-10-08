<?php

namespace App\Application\Handler\Campaign;

use App\Application\Command\Campaign\SendMessageCommand;
use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Event;
use App\Domain\Campaign\Message;
use App\Infrastructure\Campaign\Contracts\TransportInterface;
use App\Infrastructure\Campaign\Repository\CampaignRepository;
use App\Infrastructure\Campaign\Repository\MessageRepository;
use App\Infrastructure\Project\Parser;
use Cake\Chronos\Chronos;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SendMessageHandler
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
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var TransportInterface
     */
    protected $transport;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var Parser
     */
    protected $parser;

    public function __construct(
        MessageRepository $repository,
        CampaignRepository $campaignRepository,
        TransportInterface $transport,
        LoggerInterface $log,
        EventDispatcherInterface $dispatcher,
        Parser $parser
    ) {
        $this->repository = $repository;
        $this->campaignRepository = $campaignRepository;
        $this->log = $log;
        $this->transport = $transport;
        $this->dispatcher = $dispatcher;
        $this->parser = $parser;
    }

    /**
     * Handles command.
     *
     * @param SendMessageCommand $command
     */
    public function handle(SendMessageCommand $command)
    {
        $message = $command->message();
        $campaign = $message->getCampaign();

        if (!$message->isQueued()) {
            $this->log->error("Can't send message. It's not queued. Status: ".$message->getStatus());

            return;
        }

        if (!$campaign->isSending()) {
            $this->log->error("Can't send message. Campaign status is not sending. Status: ".$campaign->getStatus());
            $message->setStatus(Message::STATUS_PAUSED);
            $this->repository->save($message);

            return;
        }

        $message->setContents(
            $this->parser->parseFullHtml($campaign->getProject())
        );

        $this->parser->processMessageContents($message);

        $this->dispatchSendingEvent($message);

        $this->log->info('Attempt to send message: '.$message->getId());

        try {
            if ($this->transport->send($message)) {
                $this->log->info('Message sent success: '.$message->getId());
                $message->setStatus(Message::STATUS_SENT);
                $message->setSentAt(Chronos::now()->toMutable());
                $this->dispatchSentEvent($message);
            } else {
                $this->log->info('Message sent failed: '.$message->getId());
                $message->setStatus(Message::STATUS_FAILED);
                $this->dispatchSendFailedEvent($message);
            }
            $this->repository->save($message);
        } catch (\Exception $e) {
            $this->log->critical('Error when sending message: #'.$message->getId());
            $this->log->critical($e);
            $message->setStatus(Message::STATUS_FAILED);
            $this->dispatchSendFailedEvent($message);
            $this->repository->save($message);
        }

        if (0 == $this->repository->getQueuedMessagesCount($campaign)
            && 0 == $this->repository->getPendingMessagesCount($campaign)) {
            $this->log->info('Marking campaign as sent: '.$campaign->getId());
            $this->markCampaignAsDone($campaign);
        }
    }

    /**
     * Dispatching sending event.
     *
     * @param $message
     */
    protected function dispatchSendingEvent($message)
    {
        $this->dispatcher->dispatch(
            Event\MessageSending::NAME,
            new Event\MessageSending($message)
        );
    }

    /**
     * Dispatching sent event when messages has been successfully sent.
     *
     * @param $message
     */
    protected function dispatchSentEvent($message)
    {
        $this->dispatcher->dispatch(
            Event\MessageSent::NAME,
            new Event\MessageSent($message)
        );
    }

    /**
     * Dispatching failed sent event.
     *
     * @param $message
     */
    protected function dispatchSendFailedEvent($message)
    {
        $this->dispatcher->dispatch(
            Event\MessageSendFailed::NAME,
            new Event\MessageSendFailed($message)
        );
    }

    /**
     * If there is no queued messages, mark campaign as send.
     *
     * @param Campaign $campaign
     */
    protected function markCampaignAsDone(Campaign $campaign)
    {
        $this->log->info(
            "Campaign send successfully {$campaign->getId()} - {$campaign->getQueuedMessageCount()}"
        );

        $campaign->setStatus(Campaign::STATUS_SENT);
        $campaign->setSendingEndsAt(new \DateTime());
        $this->campaignRepository->merge($campaign);
    }
}
