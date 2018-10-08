<?php

namespace App\Test\Application\Command\Account;

use App\Application\Command\Campaign\RestoreQueuedMessagesCommand;
use App\Domain\Campaign\Campaign;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestoreQueuedMessagesCommandTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_creates_command_correctly()
    {
        $campaign = new Campaign();
        $campaign->setStatus(Campaign::STATUS_SENDING);
        $command = new RestoreQueuedMessagesCommand($campaign);

        $this->assertEquals($command->campaign(), $campaign);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_when_campaign_has_invalid_status()
    {
        $campaign = new Campaign();
        $campaign->setStatus(Campaign::STATUS_DRAFT);
        new RestoreQueuedMessagesCommand($campaign);
    }
}
