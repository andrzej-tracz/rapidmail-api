<?php

namespace App\Infrastructure\Doctrine\EventListener;

use App\Domain\Account\BelongsToAccount;
use App\Domain\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CurrentContextListener
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $resource = $event->getRequest()->attributes->get('data');
        $token = $this->tokenStorage->getToken();

        if ($token && in_array($request->getMethod(), [Request::METHOD_POST])) {
            $user = $token->getUser();

            if ($resource instanceof BelongsToAccount) {
                /** @var $user User */
                $account = $user->getActiveProfile()->getAccount();
                $resource->setAccount($account);
            }

            if (method_exists($resource, 'setUser')) {
                $resource->setUser($user);
            }
        }
    }
}
