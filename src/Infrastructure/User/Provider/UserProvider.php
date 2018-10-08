<?php

namespace App\Infrastructure\User\Provider;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Andrzej Tracz <andrzej.tracz7@gmail.com>
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var UserLoaderInterface|EntityRepository
     */
    private $userLoader;

    /**
     * @param UserLoaderInterface $userLoader
     */
    public function __construct(UserLoaderInterface $userLoader)
    {
        $this->userLoader = $userLoader;
    }

    /**
     * @param string $username
     *
     * @return UserInterface
     */
    public function loadUserByUsername($username)
    {
        return $this->userLoader->loadUserByUsername($username);
    }

    /**
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);

        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->userLoader->find($user->getId());
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->userLoader->getClassName() === $class
        || is_subclass_of($class, $this->userLoader->getClassName());
    }
}
