<?php

declare(strict_types=1);

namespace App\Domain\Campaign;

use App\Application\Model\SimpleSerializable;
use App\Application\Model\SoftDeleteable;
use App\Application\Model\Timestampable;
use App\Domain\Account\Account;
use App\Domain\Account\BelongsToAccount;
use App\Domain\Project\Project;
use App\Domain\Subscriber\SubscriberList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Campaign.
 *
 * @ORM\Entity(repositoryClass="App\Infrastructure\Campaign\Repository\CampaignRepository")
 * @Gedmo\SoftDeleteable()
 * @ORM\HasLifecycleCallbacks()
 */
class Campaign implements BelongsToAccount, \Serializable
{
    use Timestampable, SoftDeleteable, SimpleSerializable;

    const STATUS_DRAFT = 'draft';
    const STATUS_SENDING = 'sending';
    const STATUS_PAUSED = 'paused';
    const STATUS_SENT = 'done';

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
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_DRAFT;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $preheader;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $fromName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email(checkMX = true)
     * @ORM\Column(type="string", length=255)
     */
    private $fromEmail;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email(checkMX = true)
     * @ORM\Column(type="string", length=255)
     */
    private $replyTo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Subscriber\SubscriberList", inversedBy="campaigns")
     * @Assert\NotNull()
     */
    private $receiversList;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Project\Project", inversedBy="campaigns")
     * @Assert\NotNull()
     */
    private $project;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Domain\Campaign\Message", mappedBy="campaign", fetch="EXTRA_LAZY")
     * @Assert\NotNull()
     */
    private $messages;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": "false"})
     */
    private $isScheduled = 0;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sendingStartsAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sendingEndsAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Account\Account")
     * @Assert\NotBlank()
     *
     * @var Account
     */
    private $account;

    /**
     * @return int
     */
    public function getId(): ?int
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
     * @param ?string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param ?string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPreheader(): ?string
    {
        return $this->preheader;
    }

    /**
     * @param ?string $preheader
     */
    public function setPreheader(?string $preheader): void
    {
        $this->preheader = $preheader;
    }

    /**
     * @return string
     */
    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    /**
     * @param ?string $fromName
     */
    public function setFromName(?string $fromName): void
    {
        $this->fromName = $fromName;
    }

    /**
     * @return string
     */
    public function getFromEmail(): ?string
    {
        return $this->fromEmail;
    }

    /**
     * @param ?string $fromEmail
     */
    public function setFromEmail(?string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return string
     */
    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    /**
     * @param ?string $replyTo
     */
    public function setReplyTo(?string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return SubscriberList
     */
    public function getReceiversList(): ?SubscriberList
    {
        return $this->receiversList;
    }

    /**
     * @param SubscriberList $receiversList
     */
    public function setReceiversList(SubscriberList $receiversList): void
    {
        $this->receiversList = $receiversList;
    }

    /**
     * @return Project
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /**
     * @return bool
     */
    public function isScheduled(): ?bool
    {
        return $this->isScheduled;
    }

    /**
     * @param bool $isScheduled
     */
    public function setIsScheduled(bool $isScheduled): void
    {
        $this->isScheduled = $isScheduled;
    }

    /**
     * @return \DateTime
     */
    public function getSendingStartsAt(): ?\DateTime
    {
        return $this->sendingStartsAt;
    }

    /**
     * @param \DateTime $sendingStartsAt
     */
    public function setSendingStartsAt(?\DateTime $sendingStartsAt = null): void
    {
        $this->sendingStartsAt = $sendingStartsAt;
    }

    /**
     * @return \DateTime
     */
    public function getSendingEndsAt(): ?\DateTime
    {
        return $this->sendingEndsAt;
    }

    /**
     * @param \DateTime $sendingEndsAt
     */
    public function setSendingEndsAt(?\DateTime $sendingEndsAt): void
    {
        $this->sendingEndsAt = $sendingEndsAt;
    }

    /**
     * @return Account
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     *
     * @return mixed
     */
    public function setAccount(?Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function isDraft()
    {
        return self::STATUS_DRAFT === $this->status;
    }

    public function isSending()
    {
        return self::STATUS_SENDING === $this->status;
    }

    public function isPaused()
    {
        return self::STATUS_PAUSED === $this->status;
    }

    public function isSent()
    {
        return self::STATUS_SENT === $this->status;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getHandledMessageCount()
    {
        return $this->messages->filter(function (Message $message) {
            return !$message->isQueued() && !$message->isPending();
        })->count();
    }

    public function getQueuedMessageCount()
    {
        return $this->messages->filter(function (Message $message) {
            return $message->isQueued();
        })->count();
    }

    public function getSentMessageCount()
    {
        return $this->messages->filter(function (Message $message) {
            return $message->isSent();
        })->count();
    }

    public function getTotalMessageCount()
    {
        return $this->messages->count();
    }

    public function getSendingProgress()
    {
        if (0 == $this->getTotalMessageCount()) {
            return 0;
        }

        return round($this->getSentMessageCount() / $this->getTotalMessageCount() * 100, 2);
    }

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }
}
