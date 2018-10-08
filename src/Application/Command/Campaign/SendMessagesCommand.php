<?php

namespace App\Application\Command\Campaign;

use App\Domain\Campaign\Campaign;
use App\Infrastructure\Queue\Queueable;
use App\Infrastructure\Queue\ShouldQueue;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

class SendMessagesCommand extends Queueable implements ShouldQueue
{
    /**
     * @var Campaign
     *
     * @Assert\Valid()
     */
    private $campaign;

    public function __construct(Campaign $campaign)
    {
        if (!$campaign->isSending()) {
            throw new InvalidArgumentException(
                'Campaign must have sending status for sending messages'
            );
        }

        $this->campaign = $campaign;
    }

    public function campaign()
    {
        return $this->campaign;
    }
}
