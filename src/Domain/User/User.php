<?php

namespace App\Domain\User;

use App\Application\Model\SoftDeleteable;
use App\Application\Model\Timestampable;
use App\Domain\Profile\Profile;
use App\Domain\Template\PurchasedTemplate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Infrastructure\User\Repository\UserRepository")
 * @UniqueEntity(fields="email")
 * @UniqueEntity(fields="username")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable()
 */
class User implements UserInterface, \Serializable, EquatableInterface
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Input email", groups={"registration"})
     * @Assert\Email(
     *     message = "Email '{{ value }}' is not valid",
     *     checkMX = true,
     *     groups={"registration","change_email"}
     * )*
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     *
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Length(
     *   min = 4,
     *   minMessage = "Your password must be at least {{ limit }} characters long",
     *   groups={"registration"}
     * )
     */
    private $plainPassword;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isConfirmed = false;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmationToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $registerSource;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @var Role[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Domain\User\Role", inversedBy="users", cascade={"merge"}, orphanRemoval=true)
     */
    private $userRoles;

    /**
     * @var \App\Domain\Profile\Profile[]|Collection
     * @ORM\OneToMany(targetEntity="App\Domain\Profile\Profile", mappedBy="user")
     */
    private $profiles;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Profile\Profile")
     * @JoinColumn(name="current_profile_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $activeProfile;

    /**
     * @var PurchasedTemplate[]|Collection
     * @ORM\OneToMany(targetEntity="App\Domain\Template\PurchasedTemplate", mappedBy="purchasedBy")
     */
    private $purchasedTemplates;

    /**
     * @var PurchasedTemplate[]|Collection
     * @ORM\OneToMany(targetEntity="App\Domain\Project\Project", mappedBy="user")
     */
    private $projects;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username = null)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the encrypted password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $time
     *
     * @return User
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * @param bool $isConfirmed
     *
     * @return User
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = (bool) $isConfirmed;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     *
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegisterSource()
    {
        return $this->registerSource;
    }

    /**
     * @param string $registerSource
     *
     * @return $this
     */
    public function setRegisterSource($registerSource)
    {
        $this->registerSource = $registerSource;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $active
     *
     * @return User
     */
    public function setIsActive($active)
    {
        $this->isActive = (bool) $active;

        return $this;
    }

    /**
     * @return Role[]|Collection
     */
    public function getUserRoles()
    {
        return $this->userRoles;
    }

    /**
     * @param Role[]|Collection $userRoles
     *
     * @return User
     */
    public function setUserRoles($userRoles)
    {
        $this->userRoles = $userRoles;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $userRoles = $this->getUserRoles()->map(function (Role $role) {
            return $role->getRole();
        })->toArray();

        $userRoles[] = 'ROLE_USER';

        return $userRoles;
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function hasUserRole(Role $role)
    {
        return $this->userRoles->contains($role);
    }

    /**
     * Determine if user has an given role.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasRole(string $name): bool
    {
        return $this->userRoles->exists(function ($key, Role $role) use ($name) {
            return $role->getRole() === $name;
        });
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function addUserRole(Role $role)
    {
        $this->userRoles->add($role);

        return $this;
    }

    /**
     * @param Role $userRoles
     *
     * @return User
     */
    public function removeUserRole(Role $userRoles)
    {
        $this->userRoles->removeElement($userRoles);

        return $this;
    }

    /**
     * Returns array of available permissions.
     *
     * @return array
     */
    public function getPermissions()
    {
        $permissions = [];

        if ($this->hasRole(Role::ROLE_SUPER_ADMIN)) {
            $permissions[] = Permission::SYSTEM_ADMIN;
        }

        return $permissions;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        $s = serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->password,
        ]);

        return $s;
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->email,
            $this->password) = unserialize($serialized);
    }

    public function __construct()
    {
        $this->userRoles = new ArrayCollection();
        $this->profiles = new ArrayCollection();
    }

    public function __toString()
    {
        return "[#{$this->getId()}] {$this->getEmail()}";
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        return true;
    }

    /**
     * @param Profile $profile
     */
    public function setActiveProfile(Profile $profile)
    {
        $this->activeProfile = $profile;
    }

    /**
     * @return Profile
     */
    public function getActiveProfile(): Profile
    {
        return $this->activeProfile;
    }

    /**
     * @return array
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    /**
     * Add purchasedTemplate.
     *
     *
     * @return User
     */
    public function addPurchasedTemplate(PurchasedTemplate $purchasedTemplate)
    {
        $this->purchasedTemplates[] = $purchasedTemplate;

        return $this;
    }

    /**
     * Remove purchasedTemplate.
     *
     * @param  $purchasedTemplate
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
     * @return PurchasedTemplate[]|Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param PurchasedTemplate[]|Collection $projects
     */
    public function setProjects($projects): void
    {
        $this->projects = $projects;
    }
}
