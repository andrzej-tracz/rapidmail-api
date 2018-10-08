<?php

namespace App\Domain\User;

interface BelongsToUser
{
    /**
     * @return User
     */
    public function getUser(): User;

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function setUser(User $user);
}
