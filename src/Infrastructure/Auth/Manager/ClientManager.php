<?php

namespace App\Infrastructure\Auth\Manager;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\OAuthServerBundle\Entity\ClientManager as FOSClientManager;

class ClientManager extends FOSClientManager implements ClientManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findClientByPublicId($publicId)
    {
        $client = $this->findClientBy([
            'randomId' => $publicId,
        ]);

        return $client;
    }
}
