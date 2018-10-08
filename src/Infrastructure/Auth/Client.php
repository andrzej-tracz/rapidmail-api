<?php

namespace App\Infrastructure\Auth;

use App\Domain\User\User;
use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @see https://github.com/FriendsOfSymfony/FOSOAuthServerBundle/blob/master/Resources/doc/index.md
 *
 * @ORM\Table(name="oauth_client")
 * @ORM\Entity(repositoryClass="App\Infrastructure\Auth\Repository\ClientRepository")
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User")
     */
    protected $user;

    public function __construct()
    {
        parent::__construct();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
