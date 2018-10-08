<?php

namespace App\Application\Handler\Account;

use App\Application\Command\Account\CreateAccountCommand;
use App\Domain\Account\Account;
use App\Domain\Profile\Profile;
use App\Domain\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAccountHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    /**
     * CreateAccountHandler constructor.
     *
     * @param EntityManagerInterface       $manager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    /**
     * Handles account creation command.
     *
     * @param CreateAccountCommand $command
     *
     * @return Account
     *
     * @throws \Exception
     */
    public function handle(CreateAccountCommand $command)
    {
        $user = new User();
        $user->setUsername($command->email());
        $user->setEmail($command->email());
        $user->setConfirmationToken(sha1(uniqid(rand(), true)));
        $user->setPassword(
            $this->encoder->encodePassword($user, $command->plainPassword())
        );

        $this->manager->persist($user);

        $account = new Account();
        $account->setEmail($command->email());
        $account->setName($command->name());
        $account->setCreator($user);

        $this->manager->persist($account);

        $profile = new Profile();
        $profile->setEmail($user->getEmail());
        $profile->setName($user->getEmail());
        $profile->setUser($user);
        $profile->setAccount($account);

        $this->manager->persist($profile);

        $user->setActiveProfile($profile);
        $this->manager->persist($user);

        $this->manager->flush();

        return $account;
    }
}
