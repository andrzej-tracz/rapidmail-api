<?php

namespace App\UI\Http\Web\OAuth;

use App\Application\Command\Account\CreateAccountCommand;
use App\Infrastructure\Auth\OAuth\EnvatoUser;
use App\Infrastructure\Bus\CommandBusInterface;
use App\Infrastructure\User\Repository\UserRepository;
use App\Infrastructure\Utils\Str;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EnvatoOAuthController extends Controller
{
    /**
     * Initialize OAuth process.
     *
     * @Route("/authorize/envato", name="connect_envato")
     */
    public function connectAction()
    {
        return $this->getOAUthClient()->redirect();
    }

    /**
     * Handles Envato authorization.
     *
     * @Route("/authorize/envato/check", name="connect_envato_check")
     */
    public function connectCheckAction(
        UserRepository $provider,
        CommandBusInterface $bus
    ) {
        $client = $this->getOAUthClient();
        /** @var $envatoUser EnvatoUser */
        $envatoUser = $client->fetchUser();
        $user = $provider->findOneBy($envatoUser->toArray());

        if (!$user) {
            $command = new CreateAccountCommand(
                $envatoUser->getId(),
                $envatoUser->getEmail(),
                Str::random(50)
            );

            $bus->handle($command);

            $user = $provider->findOneBy([
                'email' => $envatoUser->getEmail(),
            ]);
        }

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        return new RedirectResponse(
            $this->generateUrl('index')
        );
    }

    /**
     * Resolves OAuth client instance.
     *
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2Client
     */
    protected function getOAUthClient()
    {
        return $this->get('oauth2.registry')->getClient('envato');
    }
}
