<?php

namespace App\Application\Command\Campaign;

use App\Domain\Campaign\Campaign;
use Symfony\Component\Validator\Constraints as Assert;

class SyncSubscribersCommand
{
    /**
     * @var Campaign
     *
     * @Assert\Valid()
     */
    private $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function campaign()
    {
        return $this->campaign;
    }
}
