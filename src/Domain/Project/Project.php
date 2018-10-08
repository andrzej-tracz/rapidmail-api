<?php

namespace App\Domain\Project;

use App\Application\Model\Timestampable;
use App\Domain\Account\Account;
use App\Domain\Account\BelongsToAccount;
use App\Domain\Template\Template;
use App\Domain\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project.
 *
 * @ORM\Entity(repositoryClass="App\Infrastructure\Project\Repository\ProjectRepository")
 */
class Project implements BelongsToAccount
{
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="contents", type="json_array", nullable=true)
     */
    private $contents;

    /**
     * Many user templates belongs to company.
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\Account\Account", inversedBy="projects")
     * @Assert\NotNull()
     */
    private $account;

    /**
     * Many user templates belongs to user.
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User", inversedBy="projects")
     * @Assert\NotNull()
     */
    private $user;

    /**
     * Many user templates belongs to template.
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\Template\Template", inversedBy="projects")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false)
     * @Assert\NotNull()
     */
    private $template;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Campaign\Campaign", mappedBy="project")
     */
    private $campaigns;

    public function __construct()
    {
        $this->campaigns = new ArrayCollection();
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

    /**
     * @return mixed
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * @param mixed $account
     */
    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set contents.
     *
     * @param string $contents
     *
     * @return $this
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get contents.
     *
     * @return []
     */
    public function getContents()
    {
        return $this->contents;
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
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate(Template $template): void
    {
        $this->template = $template;
    }

    public function __toString()
    {
        return "[#{$this->getId()}] {$this->getName()}";
    }
}
