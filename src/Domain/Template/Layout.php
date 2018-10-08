<?php

namespace App\Domain\Template;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Layout.
 *
 * @ORM\Table(name="layout")
 * @ORM\Entity()
 */
class Layout
{
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
     * @ORM\Column(name="name", type="string", unique=true, length=255)
     * @Assert\NotNull(message="Layout name must be provided")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="header", type="text")
     *
     * @Assert\NotNull(message="Header section must be provided")
     */
    private $header;

    /**
     * @var string
     *
     * @ORM\Column(name="footer", type="text")
     * @Assert\NotNull(message="Footer section must be provided")
     */
    private $footer;

    /**
     * One Layout has many templates.
     *
     * @OneToMany(targetEntity="App\Domain\Template\Template", mappedBy="layout")
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $templates;

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
     * @return Layout
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
     * Set header.
     *
     * @param string $header
     *
     * @return Layout
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header.
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set footer.
     *
     * @param string $footer
     *
     * @return Layout
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Get footer.
     *
     * @return string
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Add section.
     *
     *
     * @return Layout
     */
    public function addTemplate(Template $template)
    {
        $this->templates[] = $template;

        return $this;
    }

    /**
     * Remove section.
     */
    public function removeTemplate(Template $template)
    {
        $this->templates->removeElement($template);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->templates = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
