<?php

namespace App\Infrastructure\User\Provider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Domain\User\User;
use App\Infrastructure\User\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserCollectionDataProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserMe constructor.
     *
     * @param UserRepository        $repository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(UserRepository $repository, TokenStorageInterface $tokenStorage)
    {
        $this->repository = $repository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns collection of Users scoped by account.
     *
     * @param string      $resourceClass
     * @param string|null $operationName
     *
     * @return array|mixed|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        /** @var $currentUser User */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $account = $currentUser->getActiveProfile()->getAccount();

        return $this->repository->findUsersByAccount($account);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }
}
