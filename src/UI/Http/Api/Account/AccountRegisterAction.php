<?php

namespace App\UI\Http\Api\Account;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Application\Command\Account\CreateAccountCommand;
use App\Application\Command\User\SendConfirmationEmailCommand;
use App\Domain\Account\Account;
use App\Infrastructure\Bus\CommandBusInterface;
use League\Tactician\Bundle\Middleware\InvalidCommandException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountRegisterAction extends Controller
{
    /**
     * @Route(
     *     name="account_register",
     *     path="/api/account/register",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=Account::class,
     *         "_api_receive"=false,
     *         "_api_item_operation_name"="account_register"
     *     }
     * )
     */
    public function __invoke(Request $request, CommandBusInterface $bus): Account
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        try {
            $command = new CreateAccountCommand($name, $email, $password);
            /** @var $account Account */
            $account = $bus->handle($command);

            $confirmation = new SendConfirmationEmailCommand($account->getCreator());
            $bus->handle($confirmation);

            return $account;
        } catch (InvalidCommandException $e) {
            throw new ValidationException($e->getViolations());
        }
    }
}
