<?php

namespace App\UI\Http\Api\User;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Application\Command\User\ConfirmUserCommand;
use App\Domain\User\User;
use App\Infrastructure\Bus\CommandBusInterface;
use League\Tactician\Bundle\Middleware\InvalidCommandException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmUserAction
{
    /**
     * @Route(
     *     name="api_user_confirmation",
     *     path="/api/register/confirm",
     *     methods={"PUT"},
     *     defaults={
     *      "_api_resource_class"=User::class,
     *      "_api_receive"=false,
     *      "_api_item_operation_name"="api_user_confirmation"
     *      }
     * )
     *
     * @return User|null
     */
    public function __invoke(Request $request, CommandBusInterface $bus)
    {
        $token = $request->request->get('token');

        try {
            $command = new ConfirmUserCommand($token);
            $user = $bus->handle($command);

            return $user;
        } catch (InvalidCommandException $e) {
            throw new ValidationException($e->getViolations());
        }
    }
}
