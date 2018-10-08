<?php

namespace App\Application\Command\Account;

use Symfony\Component\Validator\Constraints as Assert;
use App\Infrastructure\Validator\Constraints\UniqueIn;

class CreateAccountCommand
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email(checkMX = true)
     * @UniqueIn(entityClass="App\Domain\User\User", field="email", message="This email has been already used")
     */
    protected $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="5")
     */
    protected $password;

    /**
     * CreateAccountCommand constructor.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(?string $name, ?string $email, ?string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name ?? $this->email;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function plainPassword(): string
    {
        return $this->password;
    }
}
