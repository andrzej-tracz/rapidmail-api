<?php

namespace App\Infrastructure\Subscriber\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class Subscriber
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $surname;

    /**
     * @var string
     * @Assert\Email()
     */
    private $email;

    /**
     * @param array $attributes
     *
     * @return static
     */
    public static function fromArray(array $attributes = [])
    {
        $email = $attributes['email'] ?? '';
        $name = $attributes['name'] ?? '';
        $surname = $attributes['surname'] ?? '';

        return new static($email, $name, $surname);
    }

    /**
     * SubscriberDto constructor.
     *
     * @param string $email
     * @param null   $name
     * @param null   $surname
     */
    public function __construct(string $email, $name = null, $surname = null)
    {
        $this->email = $email;
        $this->name = $name;
        $this->surname = $surname;
    }

    public function email()
    {
        return $this->email;
    }
}
