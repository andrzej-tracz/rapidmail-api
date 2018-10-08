<?php

namespace App\Test\Application\Command\Account;

use App\Application\Command\Account\CreateAccountCommand;
use App\Application\Handler\Account\CreateAccountHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAccountHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function it_handles_command()
    {
        $command = new CreateAccountCommand(
            'My Company Account',
            'admin@google.com',
            'secret'
        );

        $em = $this->createMock(EntityManagerInterface::class);
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $handler = new CreateAccountHandler($em, $encoder);

        $handler->handle($command);

        $this->assertEquals('My Company Account', $command->name());
    }

    /**
     * @test
     */
    public function it_accept_only_particular_command()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $encoder = $this->createMock(UserPasswordEncoderInterface::class);
        $handler = new CreateAccountHandler($em, $encoder);

        $this->assertInstanceOf(CreateAccountHandler::class, $handler);

        $this->expectException(\TypeError::class);
        $handler->handle(null);
    }
}
