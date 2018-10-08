<?php

namespace App\Infrastructure\Feature\Context;

use App\Domain\User\User;
use App\Infrastructure\Auth\AccessToken;
use App\Infrastructure\Auth\Client;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class AuthContext implements Context
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
     * @Given there are created OAuth Clients:
     */
    public function thereAreCreatedOauthClients(TableNode $node)
    {
        foreach ($node as $raw) {
            $client = new Client();
            $client->setAllowedGrantTypes([
                $raw['grant_type'],
            ]);

            $client->setRandomId($raw['client_id']);
            $client->setSecret($raw['secret']);

            $this->em->persist($client);
            $this->em->flush();
        }
    }

    /**
     * @When /^there is a valid access token "(.*?)" which belongs to general user$/
     */
    public function thereIsAValidAccessTokenWhichBelongsToUserWithID1($token)
    {
        $user = $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->getQuery()
            ->getResult();

        $authClient = $this->em->createQueryBuilder()
             ->select('c')
             ->from(Client::class, 'c')
             ->getQuery()
             ->getResult();

        $accessToken = new AccessToken();
        $accessToken->setUser($user[0]);
        $accessToken->setToken($token);
        $accessToken->setClient($authClient[0]);
        $expires = new \DateTime('+30 days');
        $accessToken->setExpiresAt($expires->getTimestamp());

        $this->em->persist($accessToken);
        $this->em->flush();
    }
}
