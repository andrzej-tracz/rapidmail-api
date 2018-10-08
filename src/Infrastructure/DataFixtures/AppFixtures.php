<?php

namespace App\Infrastructure\DataFixtures;

use App\Application\Command\Account\CreateAccountCommand;
use App\Application\Command\User\ConfirmUserCommand;
use App\Domain\Account\Account;
use App\Infrastructure\Auth\Client;
use App\Infrastructure\Bus\CommandBusInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    protected $bus;

    public function __construct(UserPasswordEncoderInterface $encoder, CommandBusInterface $bus)
    {
        $this->encoder = $encoder;
        $this->bus = $bus;
    }

    public function load(ObjectManager $manager)
    {
        $this->createUser('a.tracz@maesto.net');
        $this->createUser('test-user@maesto.net');
        $this->createFakeAuthClient($manager);
    }

    private function createUser($email)
    {
        $command = new CreateAccountCommand($email, $email, 'secret');
        /** @var $account Account */
        $account = $this->bus->handle($command);

        $this->bus->handle(new ConfirmUserCommand($account->getCreator()->getConfirmationToken()));
    }

    private function createFakeAuthClient(ObjectManager $manager)
    {
        $client = new Client();
        $client->setRandomId('random');
        $client->setSecret('secret');
        $client->setAllowedGrantTypes([
            'password',
            'authorization_code',
        ]);

        $manager->persist($client);
        $manager->flush();
    }
}
