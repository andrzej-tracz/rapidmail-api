<?php

namespace App\UI\Http\Api\Account;

use App\Domain\Account\Account;
use App\Domain\User\User;
use App\Infrastructure\Account\Repository\AccountRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdateAccountAction
{
    /**
     * @Route(
     *     name="api_account_update",
     *     path="/api/account/update",
     *     methods={"PUT"},
     *     defaults={
     *          "_api_resource_class"=Account::class,
     *          "_api_item_operation_name"="api_account_update",
     *          "_api_receive"=false,
     *      }
     * )
     *
     * @return Account|null
     */
    public function __invoke(
        Request $request,
        TokenStorageInterface $tokenStorage,
        AccountRepository $repository
    ): Account {
        /** @var $user User */
        $user = $tokenStorage->getToken()->getUser();
        $account = $user->getActiveProfile()->getAccount();

        $name = $request->request->get('name');
        $email = $request->request->get('email');

        $account->setName($name);
        $account->setEmail($email);

        $repository->save($account);

        return $account;
    }
}
