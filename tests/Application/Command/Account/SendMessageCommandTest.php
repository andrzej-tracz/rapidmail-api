<?php

namespace App\Test\Application\Command\Account;

use App\Application\Command\Campaign\SendMessageCommand;
use App\Domain\Campaign\Message;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SendMessageCommandTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_creates_command_correctly()
    {
        $message = new Message();
        $message->setStatus(Message::STATUS_QUEUED);
        $command = new SendMessageCommand($message);

        $this->assertEquals($command->message(), $message);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_when_message_has_invalid_status()
    {
        $message = new Message();
        $message->setStatus(Message::STATUS_SENDING);
        new SendMessageCommand($message);
    }
}
