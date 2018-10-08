<?php

namespace App\Domain\Template;

use App\Domain\Account\Account;
use App\Domain\User\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * UserTemplate.
 *
 * @ORM\Table(
 *   uniqueConstraints={
 *     @UniqueConstraint(name="purchase_code_unique", columns={"purchase_code"}),
 *     @UniqueConstraint(name="account_template_unique", columns={"template_id", "account_id"})
 *  }
 * )
 * @UniqueEntity(
 *     fields={"purchaseCode"},
 *     message="This purchased code has been already used"
 * )
 * @ORM\Entity(repositoryClass="App\Infrastructure\Template\Repository\PurchasedTemplateRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PurchasedTemplate
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Many purchased belongs to template.
     *
     * @ManyToOne(targetEntity="App\Domain\Template\Template", inversedBy="purchases", fetch="EAGER")
     * @JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $template;

    /**
     * Many purchased belongs to user.
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User", inversedBy="purchasedTemplates")
     * @JoinColumn(name="purchased_by_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $purchasedBy;

    /**
     * Many purchased belongs to account.
     *
     * @ORM\ManyToOne(targetEntity="App\Domain\Account\Account", inversedBy="purchasedTemplates")
     * @JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $account;

    /**
     * @var string
     *
     * @ORM\Column(name="purchase_code", type="string")
     */
    private $purchaseCode;

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
     * Set template.
     *
     * @param Template $template
     *
     * @return PurchasedTemplate
     */
    public function setTemplate(Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template.
     *
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set purchasedBy.
     *
     * @param User $purchasedBy
     *
     * @return PurchasedTemplate
     */
    public function setPurchasedBy(User $purchasedBy = null)
    {
        $this->purchasedBy = $purchasedBy;

        return $this;
    }

    /**
     * Get purchasedBy.
     *
     * @return User
     */
    public function getPurchasedBy()
    {
        return $this->purchasedBy;
    }

    /**
     * Set account.
     *
     * @param Account $account
     *
     * @return PurchasedTemplate
     */
    public function setAccount(Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account.
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set purchaseCode.
     *
     * @param string $purchaseCode
     *
     * @return PurchasedTemplate
     */
    public function setPurchaseCode($purchaseCode)
    {
        $this->purchaseCode = $purchaseCode;

        return $this;
    }

    /**
     * Get purchaseCode.
     *
     * @return string
     */
    public function getPurchaseCode()
    {
        return $this->purchaseCode;
    }

    public function __toString()
    {
        return "[#{$this->getId()}] {$this->getPurchaseCode()}";
    }
}
