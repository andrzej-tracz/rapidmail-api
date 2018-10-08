<?php

namespace App\UI\Http\Api\Account;

use App\Domain\Profile\Profile;
use App\Domain\User\User;
use App\Infrastructure\User\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SwitchAccountAction
{
    /**
     * @Route(
     *     name="api_profile_set_current",
     *     path="/api/account/set-current/{profile}",
     *     defaults={
     *      "_api_resource_class"=User::class,
     *      "_api_item_operation_name"="api_profile_set_current",
     *      "_api_receive"=false,
     *      },
     *     methods={"PUT"},
     * )
     *
     * @return User
     */
    public function __invoke(Profile $profile, TokenStorageInterface $tokenStorage, UserRepository $users)
    {
        /** @var $user User */
        $user = $tokenStorage->getToken()->getUser();

        if ($user->getProfiles()->contains($profile)) {
            $user->setActiveProfile($profile);
            $users->save($user);
        }

        return $user;
    }
}
