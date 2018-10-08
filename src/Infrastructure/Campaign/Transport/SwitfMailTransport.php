<?php

namespace App\Infrastructure\Campaign\Transport;

use App\Infrastructure\Campaign\Contracts;
use Psr\Log\LoggerInterface;

class SwitfMailTransport implements Contracts\TransportInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(\Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Contracts\MessageInterface $message): bool
    {
        $this->logger->info("Sending message to: {$message->getTo()}");

        $message = (new \Swift_Message($message->getTitle()))
            ->setFrom($message->getFromEmail(), $message->getFromName())
            ->setTo($message->getTo())
            ->setBody($message->getContents(), 'text/html')
        ;

        $recipients = $this->mailer->send($message);

        $this->logger->info('Recipes: '.$recipients);

        return $recipients > 0;
    }
}
