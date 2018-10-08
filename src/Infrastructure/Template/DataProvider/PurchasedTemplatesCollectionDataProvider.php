<?php

namespace App\Infrastructure\Template\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Domain\Template\PurchasedTemplate;
use App\Domain\User\User;
use App\Infrastructure\Template\Repository\PurchasedTemplateRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PurchasedTemplatesCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var PurchasedTemplateRepository
     */
    protected $repository;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(PurchasedTemplateRepository $repository, TokenStorageInterface $storage)
    {
        $this->repository = $repository;
        $this->tokenStorage = $storage;
    }

    /**
     * Retrieves a collection.
     *
     * @return array|\Traversable
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        /** @var $user User */
        $user = $this->tokenStorage->getToken()->getUser();
        $account = $user->getActiveProfile()->getAccount();

        return $this->repository->findPurchasedByAccount($account);
    }

    /**
     * @param string      $resourceClass
     * @param string|null $operationName
     * @param array       $context
     *
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return PurchasedTemplate::class === $resourceClass;
    }
}
