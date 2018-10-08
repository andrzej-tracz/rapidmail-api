<?php

namespace App\Infrastructure\Template\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Domain\Template\Template;
use App\Infrastructure\Template\Repository\TemplateRepository;
use App\UI\Http\Api\Template\TemplateDetailsAction;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TemplateItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var TemplateRepository
     */
    private $repository;

    /**
     * @param TemplateRepository    $repository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TemplateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string      $resourceClass
     * @param int|string  $idOrName
     * @param string|null $operationName
     * @param array       $context
     *
     * @return Template|object
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getItem(string $resourceClass, $idOrName, string $operationName = null, array $context = [])
    {
        if (TemplateDetailsAction::ACTION_NAME === $operationName) {
            return $this->repository->findByNameWithDetails($idOrName);
        }

        return $this->repository->find($idOrName);
    }

    /**
     * Only supports templates.
     *
     * @param string      $resourceClass
     * @param string|null $operationName
     * @param array       $context
     *
     * @return bool
     */
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Template::class === $resourceClass;
    }
}
