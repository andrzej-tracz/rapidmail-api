<?php

namespace App\UI\Cli\Auth;

use App\Infrastructure\Auth\Client;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateOAuthClientCommand extends ContainerAwareCommand
{
    protected $clientManger;

    public function __construct(ClientManagerInterface $clientManager)
    {
        parent::__construct();

        $this->clientManger = $clientManager;
    }

    protected function configure()
    {
        $this
            ->setName('oauth:client:create')
            ->setDescription('Create OAuth Client')
            ->addArgument(
                'grantType',
                InputArgument::REQUIRED,
                'Grant Type?'
            )
            ->addArgument(
                'redirectUri',
                InputArgument::OPTIONAL,
                'Redirect URI?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redirectUri = $input->getArgument('redirectUri');
        $grantType = $input->getArgument('grantType');

        /** @var Client $client */
        $client = $this->clientManger->createClient();
        $client->setRedirectUris($redirectUri ? [$redirectUri] : []);
        $client->setAllowedGrantTypes([$grantType]);
        $this->clientManger->updateClient($client);

        $output->writeln(sprintf(
            '<info>The client <comment>%s</comment> was created with <comment>%s</comment> as public id and <comment>%s</comment> as secret</info>',
            $client->getId(),
            $client->getPublicId(),
            $client->getSecret()
        ));
    }
}
