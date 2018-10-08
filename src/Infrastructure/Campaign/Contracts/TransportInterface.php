<?php

namespace App\Infrastructure\Campaign\Contracts;

interface TransportInterface
{
    /**
     * Sends given message, returns true when message has been send successfully
     * false otherwise.
     *
     * @param MessageInterface $message
     *
     * @return bool
     */
    public function send(MessageInterface $message): bool;
}
