<?php

namespace App\Domain\Template;

use App\Application\Model\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Template.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Infrastructure\Template\Repository\TemplateRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="name", message="template.name.unique")
 * @Vich\Uploadable
 */
class Template implements \Serializable
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Layout
     *
     * @ManyToOne(targetEntity="App\Domain\Template\Layout", inversedBy="templates")
     * @JoinColumn(name="layout_id", referencedColumnName="id")
     *
     * @Assert\NotNull()
     */
    private $layout;

    /**
     * @var string
     *
     * @ORM\Column(name="layout_html", type="text", nullable=true)
     */
    private $layoutHtml;

    /**
     * @var string
     *
     * @ORM\Column(name="head_styles", type="text", nullable=true)
     */
    private $headStyles;

    /**
     * @var string
     *
     * @ORM\Column(name="preheader", type="text", nullable=true)
     */
    private $preheader;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_public", type="boolean", options={"default": "1"})
     */
    private $isPublic;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default": "0"})
     */
    private $isActive;

    /**
     * @Vich\UploadableField(
     *     mapping="template_archive",
     *     fileNameProperty="archive.name",
     *     size="archive.size",
     *     mimeType="archive.mimeType",
     *     originalName="archive.originalName"
     * )
     *
     * @var File
     */
    private $archiveFile;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var string
     */
    private $archive;

    /**
     * One Template has many sections.
     *
     * @OneToMany(targetEntity="App\Domain\Template\TemplateSection", mappedBy="template")
     * @OrderBy({"id" = "ASC"})
     */
    private $sections;

    /**
     * One Template has many purchases.
     *
     * @OneToMany(targetEntity="App\Domain\Template\PurchasedTemplate", mappedBy="template")
     */
    private $purchases;

    /**
     * One Template has many projects.
     *
     * @OneToMany(targetEntity="App\Domain\Project\Project", mappedBy="template")
     */
    private $projects;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->archive = new EmbeddedFile();
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
     * @return Template
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
     * Set layout.
     *
     * @param string $layout
     *
     * @return Template
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Get layout.
     *
     * @return Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set isPublic.
     *
     * @param bool $isPublic
     *
     * @return Template
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic.
     *
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Template
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param File|UploadedFile $archiveFile
     *
     * @throws \Exception
     */
    public function setArchiveFile(File $archiveFile = null)
    {
        $this->archiveFile = $archiveFile;

        if ($archiveFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return File|null
     */
    public function getArchiveFile()
    {
        return $this->archiveFile;
    }

    /**
     * @param EmbeddedFile $archive
     */
    public function setArchive(EmbeddedFile $archive)
    {
        $this->archive = $archive;
    }

    /**
     * @return EmbeddedFile
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Add section.
     *
     * @param TemplateSection $section
     *
     * @return Template
     */
    public function addSection(TemplateSection $section)
    {
        $this->sections[] = $section;

        return $this;
    }

    /**
     * Remove section.
     *
     * @param TemplateSection $section
     */
    public function removeSection(TemplateSection $section)
    {
        $this->sections->removeElement($section);
    }

    /**
     * Get sections.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * Set layoutHtml.
     *
     * @param string $layoutHtml
     *
     * @return Template
     */
    public function setLayoutHtml($layoutHtml)
    {
        $this->layoutHtml = $layoutHtml;

        return $this;
    }

    /**
     * Get layoutHtml.
     *
     * @return string
     */
    public function getLayoutHtml()
    {
        return $this->layoutHtml;
    }

    /**
     * Set headStyles.
     *
     * @param string $headStyles
     *
     * @return $this
     */
    public function setHeadStyles($headStyles)
    {
        $this->headStyles = $headStyles;

        return $this;
    }

    /**
     * Get headStyles.
     *
     * @return string
     */
    public function getHeadStyles()
    {
        return $this->headStyles;
    }

    /**
     * Set preheader.
     *
     * @param string $preheader
     *
     * @return Template
     */
    public function setPreheader($preheader)
    {
        $this->preheader = $preheader;

        return $this;
    }

    /**
     * Get preheader.
     *
     * @return string
     */
    public function getPreheader()
    {
        return $this->preheader;
    }

    /**
     * Add purchase.
     *
     * @param PurchasedTemplate $purchase
     *
     * @return Template
     */
    public function addPurchase(PurchasedTemplate $purchase)
    {
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchase.
     *
     * @param PurchasedTemplate $purchase
     */
    public function removePurchase(PurchasedTemplate $purchase)
    {
        $this->purchases->removeElement($purchase);
    }

    /**
     * Get purchases.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    public function __toString()
    {
        return "[#{$this->getId()}] {$this->getName()}";
    }

    /**
     * @return mixed
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @param mixed $projects
     */
    public function setProjects($projects): void
    {
        $this->projects = $projects;
    }

    /**
     * String representation of object.
     *
     * @see http://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     *
     * @since 5.1.0
     */
    public function serialize()
    {
        $serialized = serialize([
            $this->id,
            $this->name,
        ]);

        return $serialized;
    }

    /**
     * Constructs the object.
     *
     * @see http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name) = unserialize($serialized);
    }
}
