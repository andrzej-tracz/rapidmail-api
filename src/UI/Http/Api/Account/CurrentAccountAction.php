<?php

namespace App\UI\Http\Api\Account;

use App\Domain\Account\Account;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CurrentAccountAction
{
    /**
     * @Route(
     *     name="api_account_show_current",
     *     path="/api/account",
     *     methods={"GET"},
     *     defaults={
     *     "_api_resource_class"=Account::class,
     *     "_api_item_operation_name"="api_current_account_show",
     *     "_api_receive"=false
     *     }
     * )
     *
     * @return Account|null
     */
    public function __invoke(TokenStorageInterface $tokenStorage): Account
    {
        $user = $tokenStorage->getToken()->getUser();

        return $user->getActiveProfile()->getAccount();
    }
}
