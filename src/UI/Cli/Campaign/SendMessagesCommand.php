<?php

namespace App\UI\Cli\Campaign;

use App\Domain\Campaign\Campaign;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\Campaign\Repository\CampaignRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMessagesCommand extends ContainerAwareCommand
{
    /**
     * @var CampaignRepository
     */
    protected $repository;

    /**
     * @var
     */
    protected $bus;

    public function __construct(CampaignRepository $repository, CommandBusInterface $bus)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this
            ->setName('app:campaigns:send')
            ->setDescription('Sends chunk of messages of given sending campaigns')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $campaign Campaign */
        $campaigns = $this->repository->findBy([
            'status' => Campaign::STATUS_SENDING,
        ]);
        $now = (new \DateTime())->format('d-m-Y H:i:s');

        if (0 === count($campaigns)) {
            $output->writeln("{$now}: No campaigns with Sending status.");
        }

        foreach ($campaigns as $campaign) {
            $output->writeln("{$now}: Fire send messages command. Campaign #{$campaign->getId()} - {$campaign->getName()}");

            $command = new \App\Application\Command\Campaign\SendMessagesCommand($campaign);
            $command->dispatchNow();

            $this->bus->handle(
                $command
            );
        }
    }
}
