<?php

namespace App\Application\Handler\User;

use App\Application\Command\User\ConfirmUserCommand;
use App\Domain\User\User;
use App\Infrastructure\User\Repository\UserRepository;

class ConfirmUserCommandHandler
{
    /**
     * @var UserRepository
     */
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(ConfirmUserCommand $command)
    {
        $token = $command->confirmationToken();

        /** @var $user User */
        $user = $this->repository->findOneBy([
            'confirmationToken' => $token,
        ]);

        $user->setIsConfirmed(true);
        $user->setConfirmationToken(null);

        return $this->repository->save($user);
    }
}
