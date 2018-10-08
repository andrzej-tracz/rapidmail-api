<?php

namespace App\Application\Handler\Template;

use App\Application\Command\Template\ActivateTemplateCommand;
use App\Domain\Template\PurchasedTemplate;
use App\Domain\User\User;
use App\Infrastructure\Template\Repository\PurchasedTemplateRepository;
use App\Infrastructure\Template\Repository\TemplateRepository;
use App\Infrastructure\Template\Service\TemplateService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActivateTemplateHandler
{
    /**
     * @var TemplateService
     */
    protected $service;

    /**
     * @var TemplateRepository
     */
    protected $repository;

    /**
     * @var PurchasedTemplateRepository
     */
    protected $purchases;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(
        TemplateService $service,
        TemplateRepository $repository,
        PurchasedTemplateRepository $purchases,
        TokenStorageInterface $tokenStorage
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->purchases = $purchases;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Handles activation of template by purchase code.
     *
     * @param ActivateTemplateCommand $command
     *
     * @return \App\Domain\Template\PurchasedTemplate
     */
    public function handle(ActivateTemplateCommand $command)
    {
        /** @var $user User */
        $user = $this->tokenStorage->getToken()->getUser();
        $template = $command->template();
        $code = $command->code();

        $purchase = new PurchasedTemplate();
        $purchase->setTemplate($template);
        $purchase->setPurchaseCode($code);
        $purchase->setPurchasedBy($user);
        $purchase->setAccount($user->getActiveProfile()->getAccount());
        $this->purchases->save($purchase);

        return $purchase;
    }
}
