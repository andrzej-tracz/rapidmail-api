<?php

namespace App\Test\Infrastructure\Queue;

use App\Application\Command\Account\CreateAccountCommand;
use App\Application\Command\Campaign\SendMessageCommand;
use App\Application\Command\Campaign\SendMessagesCommand;
use App\Domain\Campaign\Campaign;
use App\Domain\Campaign\Message;
use App\Infrastructure\Queue\CommandRefresher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RefresherCommandTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CommandRefresher
     */
    protected $refresher;

    protected $logger;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->refresher = new CommandRefresher($this->entityManager, $this->logger);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
        $this->refresher = null;
    }

    /**
     * @test
     */
    public function it_refreshes_a_command()
    {
        $this->assertTrue($this->refresher instanceof CommandRefresher);
        $campaigns = $this->entityManager->getRepository(Campaign::class)->findAll();
        $campaign = $campaigns[0];
        $command = new SendMessagesCommand($campaign);

        /** @var $refreshed SendMessagesCommand */
        $refreshed = $this->refresher->refreshCommand(unserialize(serialize($command)));

        $this->assertSame(
            $refreshed->campaign()->getId(),
            $command->campaign()->getId()
        );

        $this->assertSame($refreshed->shouldQueue(), $command->shouldQueue());
        $this->assertSame($refreshed->campaign()->getProject()->getId(), $command->campaign()->getProject()->getId());
    }

    /**
     * @test
     */
    public function it_refreshes_send_message_command()
    {
        $message = $this->entityManager->getRepository(Message::class)->findOneBy([
            'status' => Message::STATUS_QUEUED,
        ]);

        $command = new SendMessageCommand($message);

        /** @var $refreshed SendMessageCommand */
        $refreshed = $this->refresher->refreshCommand(unserialize(serialize($command)));

        $this->assertSame(
            $refreshed->message()->getId(),
            $command->message()->getId()
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_command_is_not_queuable()
    {
        $command = new CreateAccountCommand('newAccount', 'new@dev.local', 's@cr@t');
        $this->expectException(\TypeError::class);

        /* @var  $refreshed CreateAccountCommand */
        $this->refresher->refreshCommand(unserialize(serialize($command)));
    }
}
