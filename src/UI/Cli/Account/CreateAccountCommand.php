<?php

namespace App\UI\Cli\Account;

use App\Application\Command\User\SendConfirmationEmailCommand;
use App\Domain\Account\Account;
use App\Infrastructure\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAccountCommand extends ContainerAwareCommand
{
    /**
     * @var CommandBusInterface
     */
    protected $bus;

    public function __construct(CommandBusInterface $bus)
    {
        parent::__construct();

        $this->bus = $bus;
    }

    protected function configure()
    {
        $this
            ->setName('app:account:create')
            ->setDescription('Create New Account')
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'Email?'
            )
            ->addArgument(
                'plainPassword',
                InputArgument::REQUIRED,
                'Plain password?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('plainPassword');

        $command = new \App\Application\Command\Account\CreateAccountCommand(
            $email,
            $email,
            $plainPassword
        );

        /** @var $account Account */
        $account = $this->bus->handle($command);

        $confirmation = new SendConfirmationEmailCommand($account->getCreator());
        $this->bus->handle($confirmation);

        $output->writeln("<info>Account created ID: {$account->getId()}</info>");
    }
}
