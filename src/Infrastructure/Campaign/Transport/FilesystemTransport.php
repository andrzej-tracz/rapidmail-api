<?php

namespace App\Infrastructure\Campaign\Transport;

use App\Infrastructure\Campaign\Contracts;
use Cake\Chronos\Chronos;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemTransport implements Contracts\TransportInterface
{
    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ParameterBagInterface
     */
    protected $container;

    public function __construct(Filesystem $fs, LoggerInterface $logger, ContainerInterface $container)
    {
        $this->fs = $fs;
        $this->logger = $logger;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Contracts\MessageInterface $message): bool
    {
        $this->logger->info("Sending message to: {$message->getTo()} via Filesystem driver");

        $basePath = $this->container->getParameter('mailer_filesystem_path');
        $campaign = $message->getCampaign();
        $campaignName = join('-', [
            $campaign->getId(),
            $campaign->getName(),
        ]);
        $messageName = join('-', [
            $message->getTo(),
            Chronos::now(),
        ]);

        $path = $basePath.DIRECTORY_SEPARATOR.$campaignName;
        $fileName = $messageName.'.html';
        $fullPath = $path.DIRECTORY_SEPARATOR.$fileName;

        if (!$this->fs->exists($path)) {
            $this->fs->mkdir($path);
        }

        $this->fs->appendToFile($fullPath, $message->getContents());

        return true;
    }
}
