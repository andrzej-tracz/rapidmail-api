<?php

declare(strict_types=1);

namespace App\Domain\Campaign\Event;

use App\Domain\Campaign\Message;
use Symfony\Component\EventDispatcher\Event;

class MessageSending extends Event
{
    const NAME = 'campaign.message.sending';

    /**
     * @var Message
     */
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function message()
    {
        return $this->message;
    }
}
