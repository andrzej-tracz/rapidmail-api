<?php

namespace App\Application\Command\Campaign;

use App\Domain\Campaign\Campaign;

class RestoreQueuedMessagesCommand
{
    /**
     * @var Campaign
     */
    private $campaign;

    public function __construct(Campaign $campaign)
    {
        if (!$campaign->isSending()) {
            throw new \InvalidArgumentException(
                'Campaign must have sending status to restore queued messages'
            );
        }

        $this->campaign = $campaign;
    }

    public function campaign()
    {
        return $this->campaign;
    }
}
