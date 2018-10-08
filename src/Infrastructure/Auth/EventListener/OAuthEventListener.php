<?php

namespace App\Infrastructure\Auth\EventListener;

use FOS\OAuthServerBundle\Event\OAuthEvent;

class OAuthEventListener
{
    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
    }

    public function onPostAuthorizationProcess(OAuthEvent $event)
    {
    }
}
