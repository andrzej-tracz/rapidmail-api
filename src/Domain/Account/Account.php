<?php

declare(strict_types=1);

namespace App\Domain\Account;

use App\Application\Model\SoftDeleteable;
use App\Application\Model\Timestampable;
use App\Domain\Project\Project;
use App\Domain\Template\PurchasedTemplate;
use App\Domain\User\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Account.
 *
 * @ORM\Entity(repositoryClass="App\Infrastructure\Account\Repository\AccountRepository")
 * @UniqueEntity("email")
 * @Gedmo\SoftDeleteable()
 */
class Account
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
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var \App\Domain\Profile\Profile[]|Collection
     * @ORM\OneToMany(targetEntity="App\Domain\Profile\Profile", mappedBy="account")
     */
    private $profiles;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User")
     * @ORM\JoinColumn(name="creator_id")
     */
    private $creator;

    /**
     * @var Collection|PurchasedTemplate[]
     * @ORM\OneToMany(targetEntity="App\Domain\Template\PurchasedTemplate", mappedBy="account")
     */
    private $purchasedTemplates;

    /**
     * @var Collection|Project[]
     * @ORM\OneToMany(targetEntity="App\Domain\Project\Project", mappedBy="account")
     */
    private $projects;

    /**
     * @return int
     */
    public function getId(): ?int
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
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
     * @return $this
     */
    public function setProfiles($profiles)
    {
        $this->profiles = $profiles;

        return $this;
    }

    /**
     * @return \App\Domain\Profile\Profile[]|Collection
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @return User
     */
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    /**
     * @param User $creator
     */
    public function setCreator(User $creator): void
    {
        $this->creator = $creator;
    }

    /**
     * Add purchasedTemplate.
     *
     * @param  $purchasedTemplate
     *
     * @return $this
     */
    public function addPurchasedTemplate(PurchasedTemplate $purchasedTemplate)
    {
        $this->purchasedTemplates[] = $purchasedTemplate;

        return $this;
    }

    /**
     * Remove purchasedTemplate
     * $name.
     *
     * @param PurchasedTemplate $purchasedTemplate
     */
    public function removePurchasedTemplate(PurchasedTemplate $purchasedTemplate)
    {
        $this->purchasedTemplates->removeElement($purchasedTemplate);
    }

    /**
     * Get purchasedTemplates.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchasedTemplates()
    {
        return $this->purchasedTemplates;
    }

    /**
     * @return Project[]|Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @return $this
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;

        return $this;
    }

    public function __toString()
    {
        return "[#{$this->getId()}] {$this->getName()}";
    }
}
