<?php

namespace App\UI\Http\Api\User;

use App\Domain\User\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CurrentUserAction
{
    /**
     * @Route(
     *     name="api_users_me",
     *     path="/api/me",
     *     methods={"GET"},
     *     defaults={
     *       "_api_resource_class"=User::class,
     *       "_api_item_operation_name"="user_me",
     *       "_api_receive"=false
     *     }
     * )
     *
     * @return User|null
     */
    public function __invoke(TokenStorageInterface $tokenStorage)
    {
        return $tokenStorage->getToken()->getUser();
    }
}
