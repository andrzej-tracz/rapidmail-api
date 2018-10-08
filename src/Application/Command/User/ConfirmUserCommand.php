<?php

namespace App\Application\Command\User;

use App\Infrastructure\Validator\Constraints\ExistsIn;

class ConfirmUserCommand
{
    /**
     * @var string
     *
     * @ExistsIn(entityClass="App\Domain\User\User", field="confirmationToken")
     */
    protected $confirmationToken;

    public function __construct(string $token)
    {
        $this->confirmationToken = $token;
    }

    public function confirmationToken()
    {
        return $this->confirmationToken;
    }
}
