<?php

namespace App\Domain\Subscriber;

use App\Domain\Account\Account;
use App\Domain\Account\BelongsToAccount;
use App\Domain\User\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Subscriber\Repository\SubscriberRepository")
 */
class Subscriber implements BelongsToAccount
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "Email {{ value }} is not valid",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     *
     * @var User
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Account\Account")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     *
     * @var Account
     */
    private $account;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Subscriber\SubscriberList", inversedBy="subscribers")
     * @Assert\NotBlank()
     *
     * @var SubscriberList
     */
    private $subscribersList;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * @return $this
     */
    public function setAccount(?Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return SubscriberList
     */
    public function getSubscribersList(): ?SubscriberList
    {
        return $this->subscribersList;
    }

    /**
     * @param SubscriberList $subscribersLists
     */
    public function setSubscribersList(SubscriberList $subscribersList): void
    {
        $this->subscribersList = $subscribersList;
    }
}
