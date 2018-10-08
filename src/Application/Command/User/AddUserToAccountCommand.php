<?php

namespace App\Application\Command\User;

use App\Domain\Account\Account;
use Symfony\Component\Validator\Constraints as Assert;

class AddUserToAccountCommand
{
    /**
     * @Assert\Valid()
     *
     * @var Account
     */
    private $account;

    /**
     * @Assert\Email()
     *
     * @var string
     */
    private $email;

    public function __construct(Account $account, string $email)
    {
        $this->account = $account;
        $this->email = $email;
    }

    public function email()
    {
        return $this->email;
    }

    public function account()
    {
        return $this->account;
    }
}
