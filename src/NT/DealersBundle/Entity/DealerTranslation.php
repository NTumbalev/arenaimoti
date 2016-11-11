<?php

namespace NT\DealersBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * NT\DealersBundle\Entity\DealerTranslation.php
 *
 * @ORM\Entity
 * @ORM\Table(name="dealer_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   })}
 * )
 * @Gedmo\Loggable
 */
class DealerTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NT\DealersBundle\Entity\Dealer", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;


    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=250, nullable=true)
     */
    private $title;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Slug(fields={"title"}, separator="-", updatable=false, unique=false)
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;


    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="pin_description", type="text", nullable=true)
     */
    protected $pinDescription;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="simple_description", type="text", nullable=true)
     */
    protected $simpleDescription;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="isContact", type="boolean")
     */
    protected $isContact;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="not_in_distributors", type="boolean")
     */
    protected $notInDistributors;


    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param Media $description
     * @param string $title
     * @param string $slug
     * @param Media $image
     */
    public function __construct($locale = null, $description = null, $title = null, $slug = null, $simpleDescription = null, $pinDescription = null, $image = null, $isContact = null, $notInDistributors = null)
    {
        $this->locale = $locale;
        $this->slug = $slug;
        $this->description = $description;
        $this->title = $title;
        $this->image = $image;
        $this->simpleDescription = $simpleDescription;
        $this->pinDescription = $pinDescription;
        $this->isContact = $isContact;
        $this->notInDistributors = $notInDistributors;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Gets the value of description.
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param mixed $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of slug.
     *
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param mixed $slug the slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
    * Get image
    * @return
    */
    public function getImage()
    {
        return $this->image;
    }

    /**
    * Set image
    * @return $this
    */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
    * Get simpleDescription
    * @return
    */
    public function getSimpleDescription()
    {
        return $this->simpleDescription;
    }

    /**
    * Set simpleDescription
    * @return $this
    */
    public function setSimpleDescription($simpleDescription)
    {
        $this->simpleDescription = $simpleDescription;
        return $this;
    }

    /**
    * Get pinDescription
    * @return
    */
    public function getPinDescription()
    {
        return $this->pinDescription;
    }

    /**
    * Set pinDescription
    * @return $this
    */
    public function setPinDescription($pinDescription)
    {
        $this->pinDescription = $pinDescription;
        return $this;
    }

    /**
    * Get isContact
    * @return
    */
    public function isContact()
    {
        return $this->isContact;
    }

    /**
    * Set isContact
    * @return $this
    */
    public function setIsContact($isContact)
    {
        $this->isContact = $isContact;
        return $this;
    }

    /**
     * Gets the value of notInDistributors.
     *
     * @return mixed
     */
    public function getNotInDistributors()
    {
        return $this->notInDistributors;
    }

    /**
     * Sets the value of notInDistributors.
     *
     * @param mixed $notInDistributors the not in distributors
     *
     * @return self
     */
    public function setNotInDistributors($notInDistributors)
    {
        $this->notInDistributors = $notInDistributors;

        return $this;
    }
}