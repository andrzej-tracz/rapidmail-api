<?php

namespace App\Application\Handler\User;

use App\Application\Command\User\AddUserToAccountCommand;
use App\Domain\Profile\Profile;
use App\Domain\User\User;
use App\Infrastructure\Profile\Repository\ProfileRepository;
use App\Infrastructure\User\Repository\UserRepository;
use League\Tactician\Bundle\Middleware\InvalidCommandException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddUserToAccountHandler
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ProfileRepository
     */
    protected $profileRepository;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(
        UserRepository $userRepository,
        ProfileRepository $profileRepository,
        ValidatorInterface $validator
    ) {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
        $this->validator = $validator;
    }

    public function handle(AddUserToAccountCommand $command)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $command->email(),
        ]);

        if (null === $user) {
            $user = $this->createUser($command->email());
        }

        $profile = new Profile();
        $profile->setEmail($user->getEmail());
        $profile->setName($user->getEmail());
        $profile->setUser($user);
        $profile->setAccount($command->account());

        $constraintViolations = $this->validator->validate($profile);

        if (count($constraintViolations) > 0) {
            throw InvalidCommandException::onCommand($command, $constraintViolations);
        }

        $this->profileRepository->save($profile);

        $user->setActiveProfile($profile);
        $this->userRepository->save($user);

        return $user;
    }

    protected function createUser(string $email)
    {
        $user = new User();
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setConfirmationToken(sha1(uniqid(rand(), true)));
        $user->setPassword(sha1(uniqid(rand(), true)));

        $this->userRepository->save($user);

        return $user;
    }
}
