<?php

namespace App\Infrastructure\Auth\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class EnvatoUser implements ResourceOwnerInterface
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getEmail();
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'email' => $this->email,
        ];
    }
}
