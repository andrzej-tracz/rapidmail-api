<?php

namespace App\Application\Command\User;

use App\Domain\User\User;

class SendConfirmationEmailCommand
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function user()
    {
        return $this->user;
    }
}
