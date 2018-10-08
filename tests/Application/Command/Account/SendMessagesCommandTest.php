<?php

namespace App\Test\Application\Command\Account;

use App\Application\Command\Campaign\SendMessagesCommand;
use App\Domain\Campaign\Campaign;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SendMessagesCommandTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_creates_command_correctly()
    {
        $campaign = new Campaign();
        $campaign->setStatus(Campaign::STATUS_SENDING);
        $command = new SendMessagesCommand($campaign);

        $this->assertEquals($command->campaign(), $campaign);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_when_message_has_invalid_status()
    {
        $campaign = new Campaign();
        $campaign->setStatus(Campaign::STATUS_DRAFT);
        $command = new SendMessagesCommand($campaign);

        $this->assertEquals($command->campaign(), $campaign);
    }
}
