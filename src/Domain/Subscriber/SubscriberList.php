<?php

namespace App\Domain\Subscriber;

use App\Application\Model\SoftDeleteable;
use App\Application\Model\Timestampable;
use App\Domain\Account\Account;
use App\Domain\Account\BelongsToAccount;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Subscriber\Repository\SubscriberListRepository")
 * @Gedmo\SoftDeleteable()
 */
class SubscriberList implements BelongsToAccount
{
    use Timestampable, SoftDeleteable;

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
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Account\Account")
     * @Assert\NotBlank()
     *
     * @var Account
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Subscriber\Subscriber", mappedBy="subscribersList")
     */
    private $subscribers;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Campaign\Campaign", mappedBy="receiversList")
     */
    private $campaigns;

    public function __construct()
    {
        $this->campaigns = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * @return ArrayCollection
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }

    /**
     * @param mixed $subscribers
     */
    public function setSubscribers($subscribers): void
    {
        $this->subscribers = $subscribers;
    }

    /**
     * @return int
     */
    public function getSubscribersCount()
    {
        return $this->subscribers->count();
    }

    /**
     * @return mixed
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }

    /**
     * @param mixed $campaigns
     */
    public function setCampaigns($campaigns): void
    {
        $this->campaigns = $campaigns;
    }

    public function __toString()
    {
        return "[#{$this->getId()}] {$this->getName()}";
    }
}
