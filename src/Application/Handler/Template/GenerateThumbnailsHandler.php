<?php

namespace App\Application\Handler\Template;

use App\Application\Command\Template\GenerateThumbnailsCommand;
use App\Domain\Template\Template;
use App\Infrastructure\Template\Repository\TemplateRepository;
use App\Infrastructure\Template\Service\TemplateService;

class GenerateThumbnailsHandler
{
    /**
     * @var TemplateService
     */
    protected $service;

    /**
     * @var TemplateRepository
     */
    protected $repository;

    public function __construct(TemplateService $service, TemplateRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * Handles generation of template thumbnails.
     *
     * @param GenerateThumbnailsCommand $command
     *
     * @return \App\Domain\Template\Template
     */
    public function handle(GenerateThumbnailsCommand $command)
    {
        /** @var Template $template */
        $template = $this->repository->find($command->template()->getId());

        $this->service->generateTemplateScreenShot($template);
        $sections = $template->getSections();

        foreach ($sections as $section) {
            $this->service->generateSectionThumbnail($section);
        }

        return $template;
    }
}
