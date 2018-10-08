<?php

namespace App\UI\Cli\Template;

use App\Domain\Template\Template;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\Template\Repository\TemplateRepository;
use App\Infrastructure\Template\Service\TemplateService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTemplateThumbnails extends ContainerAwareCommand
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
     * @var
     */
    protected $bus;

    public function __construct(TemplateService $service, TemplateRepository $repository, CommandBusInterface $bus)
    {
        parent::__construct();

        $this->service = $service;
        $this->repository = $repository;
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this
            ->setName('app:template:thumbnails')
            ->setDescription('Generate Template Thumbnails')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'TemplateName?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        /** @var $template Template */
        $template = $this->repository->findOneBy([
            'name' => $name,
        ]);

        $output->writeln("Generating thumbnails: {$template->getName()} {$template->getId()}");

        $this->service->generateTemplateScreenShot($template);
        $output->writeln('ScreenShot create created.');

        $sections = $template->getSections();
        $progressBar = new ProgressBar($output, count($sections));
        $output->writeln('Generate sections thumbnails');

        $progressBar->start();

        foreach ($sections as $section) {
            $this->service->generateSectionThumbnail($section);
            $progressBar->advance();
        }

        $progressBar->finish();
    }
}
