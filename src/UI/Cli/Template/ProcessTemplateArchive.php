<?php

namespace App\UI\Cli\Template;

use App\Infrastructure\Template\Repository\TemplateRepository;
use App\Infrastructure\Template\Service\TemplateService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessTemplateArchive extends ContainerAwareCommand
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
        parent::__construct();

        $this->service = $service;
        $this->repository = $repository;
    }

    protected function configure()
    {
        $this
            ->setName('app:template:import')
            ->setDescription('Import Template from ZIP archive')
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
        $template = $this->repository->findOneBy([
            'name' => $name,
        ]);

        $this->service->importTemplateFromArchive($template);

        $output->writeln("Template successful imported: {$template->getName()}");
    }
}
