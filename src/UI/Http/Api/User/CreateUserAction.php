<?php

namespace App\UI\Http\Api\User;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Application\Command\User\AddUserToAccountCommand;
use App\Domain\User\User;
use App\Infrastructure\Bus\CommandBusInterface;
use League\Tactician\Bundle\Middleware\InvalidCommandException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateUserAction
{
    /**
     * @Route(
     *     name="api_user_post",
     *     path="/api/users",
     *     methods={"POST"},
     *     defaults={
     *     "_api_resource_class"=User::class,
     *      "_api_receive"=false,
     *     "_api_item_operation_name"="api_users_post_collection"
     *     }
     * )
     *
     * @return User|null
     */
    public function __invoke(Request $request, CommandBusInterface $bus, TokenStorageInterface $tokenStorage): User
    {
        /** @var $currentUser User */
        $currentUser = $tokenStorage->getToken()->getUser();
        $account = $currentUser->getActiveProfile()->getAccount();

        $email = $request->request->get('email');

        try {
            $command = new AddUserToAccountCommand($account, $email);
            $user = $bus->handle($command);

            return $user;
        } catch (InvalidCommandException $e) {
            throw new ValidationException($e->getViolations());
        }
    }
}
