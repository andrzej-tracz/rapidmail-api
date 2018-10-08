<?php

namespace App\Infrastructure\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use League\OAuth2\Client\Token\AccessToken;

class EnvatoClient extends OAuth2Client
{
    private $isStateless = true;

    /**
     * @param AccessToken $accessToken
     *
     * @return EnvatoUser | \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function fetchUserFromToken(AccessToken $accessToken)
    {
        return parent::fetchUserFromToken($accessToken);
    }

    /**
     * @return EnvatoUser | \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    public function fetchUser()
    {
        return parent::fetchUser();
    }
}
