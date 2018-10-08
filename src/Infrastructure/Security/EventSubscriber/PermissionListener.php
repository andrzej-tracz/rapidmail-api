<?php

namespace App\Infrastructure\Security\EventSubscriber;

use App\Domain\Account\BelongsToAccount;
use App\Infrastructure\Security\AbstractResourceVoter as Voter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class PermissionListener implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $checker;

    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    /**
     * Returns the events to which this class has subscribed.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $class = $request->attributes->get('_api_resource_class');
        $resource = $request->attributes->get('data');

        if (!$class) {
            return;
        }

        if (!in_array(BelongsToAccount::class, class_implements($class))) {
            return;
        }

        if (!key_exists($request->getMethod(), Voter::$abilities)) {
            return;
        }

        if (($resource instanceof BelongsToAccount)
            && !$this->checker->isGranted(Voter::$abilities[$request->getMethod()], $resource)
        ) {
            throw new AccessDeniedHttpException('Access denied.');
        }
    }
}
