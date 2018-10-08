<?php

namespace App\Application\Command\Campaign;

use App\Domain\Campaign\Message;
use App\Infrastructure\Queue\Queueable;
use App\Infrastructure\Queue\ShouldQueue;
use Psr\Log\InvalidArgumentException;

class SendMessageCommand extends Queueable implements ShouldQueue
{
    /**
     * @var Message
     */
    private $message;

    public function __construct(Message $message)
    {
        if (!$message->isQueued()) {
            throw new InvalidArgumentException(
                'Message must have queued status for send'
            );
        }

        $this->message = $message;
    }

    public function message()
    {
        return $this->message;
    }
}
