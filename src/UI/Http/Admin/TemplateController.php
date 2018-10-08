<?php

namespace App\UI\Http\Admin;

use App\Application\Command\Template\GenerateThumbnailsCommand;
use App\Domain\Template\Template;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\Template\Service\TemplateService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class TemplateController extends BaseAdminController
{
    /**
     * @var TemplateService
     */
    protected $service;

    /**
     * @var CommandBusInterface
     */
    protected $bus;

    public function __construct(TemplateService $service, CommandBusInterface $bus)
    {
        $this->service = $service;
        $this->bus = $bus;
    }

    /**
     * @param object $entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException* Allows applications to modify the entity associated with the item being
     *                                                created while persisting it
     *
     * @param object $entity
     *
     * @throws \App\Domain\Template\Exceptions\InvalidArchiveException
     */
    protected function persistEntity($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        /** @var $entity Template */
        if (false == $entity instanceof Template) {
            throw new \InvalidArgumentException('Only Template entity is supported');
        }

        $this->processTemplate($entity);
    }

    protected function updateEntity($entity)
    {
        /** @var $entity Template */
        if (false == $entity instanceof Template) {
            throw new \InvalidArgumentException('Only Template entity is supported');
        }

        $this->processTemplate($entity);
    }

    protected function processTemplate(Template $entity)
    {
        $this->service->importTemplateFromArchive($entity);

        $command = new GenerateThumbnailsCommand($entity);
        $this->bus->handle($command);
    }
}
