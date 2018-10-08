<?php

namespace App\Infrastructure\Campaign\EventSubscriber;

use App\Application\Command\Campaign\RestoreQueuedMessagesCommand;
use App\Application\Command\Campaign\SyncSubscribersCommand;
use App\Domain\Campaign\Campaign;
use App\Infrastructure\Bus\CommandBusInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

class CampaignSubscriber implements EventSubscriber
{
    /**
     * @var CommandBusInterface
     */
    protected $bus;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(CommandBusInterface $bus, LoggerInterface $logger)
    {
        $this->bus = $bus;
        $this->logger = $logger;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        /** @var $entity Campaign */
        $entity = $event->getObject();

        if (false == $entity instanceof Campaign) {
            return;
        }

        $this->bus->handle(
            new SyncSubscribersCommand($entity)
        );
    }

    public function preUpdate(PreUpdateEventArgs $event)
    {
        /** @var $entity Campaign */
        $entity = $event->getObject();

        if (false == $entity instanceof Campaign) {
            return;
        }

        if ($event->hasChangedField('receiversList')) {
            $this->bus->handle(
                new SyncSubscribersCommand($entity)
            );
        } else {
            $this->logger->info("Receivers list NOT changed on campaign {$entity->getId()}");
        }

        if ($event->hasChangedField('status') && $entity->isSending()) {
            $this->bus->handle(
                new RestoreQueuedMessagesCommand($entity)
            );
        }
    }
}
