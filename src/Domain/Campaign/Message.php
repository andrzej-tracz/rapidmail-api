<?php

declare(strict_types=1);

namespace App\Domain\Campaign;

use App\Application\Model\SimpleSerializable;
use App\Application\Model\Timestampable;
use App\Domain\Subscriber\Subscriber;
use App\Infrastructure\Campaign\Contracts\MessageInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Campaign Message.
 *
 * @ORM\Entity(repositoryClass="App\Infrastructure\Campaign\Repository\MessageRepository")
 * @ORM\Table(
 *   uniqueConstraints={
 *     @UniqueConstraint(name="campaign_subscriber_message_unique", columns={"campaign_id", "subscriber_id"})
 *  }
 * )
 * @UniqueEntity(
 *  fields={"campaign", "subscriber"},
 *  errorPath="subscriber",
 *  message="This subscriber has been already created for this campaign"
 * )
 */
class Message implements MessageInterface, \Serializable
{
    use Timestampable, SimpleSerializable;

    const STATUS_PENDING = 'pending';
    const STATUS_QUEUED = 'queued';
    const STATUS_PAUSED = 'paused';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';
    const STATUS_SOFT_BOUNCED = 'soft_bounced';
    const STATUS_HARD_BOUNCED = 'hard_bounced';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FAILED = 'failed';

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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $token;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_QUEUED;

    /**
     * @var Campaign
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\Campaign\Campaign", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $campaign;

    /**
     * @var Subscriber
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\Subscriber\Subscriber")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $subscriber;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sentAt = null;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $openedAt = null;

    private $contents;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Message
     */
    public function setId(int $id): Message
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return Message
     */
    public function setToken(string $token): Message
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Message
     */
    public function setStatus(string $status): Message
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Campaign
     */
    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     *
     * @return Message
     */
    public function setCampaign(Campaign $campaign): Message
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * @return Subscriber
     */
    public function getSubscriber(): ?Subscriber
    {
        return $this->subscriber;
    }

    /**
     * @param Subscriber $subscriber
     *
     * @return Message
     */
    public function setSubscriber(Subscriber $subscriber): Message
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    /**
     * @param \DateTime $sentAt
     *
     * @return Message
     */
    public function setSentAt(\DateTime $sentAt): Message
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getOpenedAt(): ?\DateTime
    {
        return $this->openedAt;
    }

    /**
     * @param \DateTime $openedAt
     *
     * @return Message
     */
    public function setOpenedAt(\DateTime $openedAt): Message
    {
        $this->openedAt = $openedAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return self::STATUS_PENDING === $this->status;
    }

    /**
     * @return bool
     */
    public function isQueued()
    {
        return self::STATUS_QUEUED === $this->status;
    }

    /**
     * @return bool
     */
    public function isPaused()
    {
        return self::STATUS_QUEUED === $this->status;
    }

    /**
     * @return bool
     */
    public function isSent()
    {
        return self::STATUS_SENT === $this->status;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return self::STATUS_FAILED === $this->status;
    }

    /**
     * @return bool
     */
    public function isSoftDebounced()
    {
        return self::STATUS_SOFT_BOUNCED === $this->status;
    }

    /**
     * @return bool
     */
    public function isHardDebounced()
    {
        return self::STATUS_HARD_BOUNCED === $this->status;
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        return self::STATUS_REJECTED === $this->status;
    }

    public function getTitle(): string
    {
        return $this->getCampaign()->getTitle() ?? '';
    }

    public function getPreheader(): string
    {
        return $this->getCampaign()->getPreheader() ?? '';
    }

    public function setContents(string $contents)
    {
        return $this->contents = $contents;
    }

    public function getContents(): string
    {
        return $this->contents ?? '';
    }

    public function getFromName(): string
    {
        return $this->getCampaign()->getFromName() ?? '';
    }

    public function getFromEmail(): string
    {
        return $this->getCampaign()->getFromEmail() ?? '';
    }

    public function getTo(): string
    {
        return $this->getSubscriber()->getEmail() ?? '';
    }

    public function __toString()
    {
        return "[#{$this->getId()}]";
    }
}
