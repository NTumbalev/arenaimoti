<?php
/**
 * This file is part of the NTEstatesBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@nt.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NT\EstatesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use NT\PublishWorkflowBundle\PublishWorkflowTrait;
use NT\PublishWorkflowBundle\PublishWorkflowInterface;
use NT\SEOBundle\SeoAwareInterface;
use NT\SEOBundle\SeoAwareTrait;

/**
 * Estate's entity
 *
 * @ORM\Table(name="estates")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="EstateRepository")
 * @Gedmo\Loggable
 *
 * @package NTEstatesBundle
 * @author  Nikolay Tumbalev <n.tumbalev@nt.bg>
 */
class Estate implements PublishWorkflowInterface, SeoAwareInterface
{
    use SeoAwareTrait;
    use PublishWorkflowTrait;
    use \NT\FrontendBundle\Traits\SocialIconsTrait;
    use \A2lix\TranslationFormBundle\Util\Gedmo\GedmoTranslatable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="number", type="string", nullable=true)
     */
    protected $number;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    protected $slug;

    /**
     * @Gedmo\Sortable
     * @Gedmo\Versioned
     * @ORM\Column(name="rank", type="integer")
     */
    protected $rank;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="simple_description", type="text", nullable=true)
     */
    protected $simpleDescription;

    /**
     * @var string
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id",  onDelete="SET NULL")
     */
    protected $gallery;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="reference_no", type="string", length=255, nullable=true)
     */
    protected $referenceNo;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="price", type="string", length=255, nullable=true)
     */
    protected $price;

    /**
     * @ORM\ManyToOne(targetEntity="NT\EstatesBundle\Entity\EstateCategory", inversedBy="estates")
     * ORM\JoinTable(name="estates_categories")
     */
    protected $estateCategories;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="area", type="string", length=255, nullable=true)
     */
    protected $area;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="beds", type="string", length=255, nullable=true)
     */
    protected $beds;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="baths", type="string", length=255, nullable=true)
     */
    protected $baths;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="parking", type="string", length=255, nullable=true)
     */
    protected $parking;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Translatable
     * @ORM\Column(name="currency", type="string", length=255, nullable=true)
     */
    protected $currency;

    /**
     * @ORM\Column(name="is_homepage", type="boolean")
     */
    protected $isHomepage = false;

    /**
     * @ORM\ManyToMany(targetEntity="NT\EstatesBundle\Entity\Attribute", inversedBy="estates")
     * @ORM\JoinTable(name="estates_attributes")
     */
    protected $attributes;

    /**
     * @ORM\ManyToOne(targetEntity="NT\EstatesBundle\Entity\Location", inversedBy="estates")
     */
    protected $location;

    /**
     * @ORM\Column(name="latitude", type="string", length=255, nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(name="longtitude", type="string", length=255, nullable=true)
     */
    protected $longitude;

    /**
     * @ORM\ManyToMany(targetEntity="Estate")
     * @ORM\JoinTable(name="estates_related_estates",
     *      joinColumns={@ORM\JoinColumn(name="estate_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="related_estates_id", referencedColumnName="id")}
     *      )
     */
    protected $relatedEstates;

    /**
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="NT\EstatesBundle\Entity\EstateTranslation", mappedBy="object", cascade={"persist", "remove"}, indexBy="locale")
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->estateCategories = new ArrayCollection();
        $this->relatedEstates = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param  string        $title
     * @return Entertainment
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime     $createdAt
     * @return Entertainment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param  \DateTime     $updatedAt
     * @return Entertainment
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return $this->getTitle() ?: 'n/a';
    }

    /**
     * Gets the value of slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Sets the value of slug.
     *
     * @param string $slug the slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Gets the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the value of description.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the value of image.
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the value of image.
     *
     * @param mixed $image the image
     *
     * @return self
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Gets the value of gallery.
     *
     * @return mixed
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Sets the value of gallery.
     *
     * @param mixed $gallery the gallery
     *
     * @return self
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getRoute()
    {
        if ($this->getEstateCategories() != null) {
            return 'estates_category_estate_view';
        } else {
            return 'estate_without_category';
        }
    }

    public function getRouteParams($params = array())
    {
        return array_merge(array('slug' => $this->getSlug()), $params);
    }

    /**
     * Gets the value of simpleDescription.
     *
     * @return mixed
     */
    public function getSimpleDescription()
    {
        return $this->simpleDescription;
    }

    /**
     * Sets the value of simpleDescription.
     *
     * @param mixed $simpleDescription the simple description
     *
     * @return self
     */
    public function setSimpleDescription($simpleDescription)
    {
        $this->simpleDescription = $simpleDescription;

        return $this;
    }

    /**
     * Gets the value of rank.
     *
     * @return mixed
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Sets the value of rank.
     *
     * @param mixed $rank the rank
     *
     * @return self
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }


    /**
     * Get the value of EstateCategories
     *
     * @return mixed
     */
    public function getEstateCategories()
    {
        return $this->estateCategories;
    }

    /**
     * Set the value of EstateCategories
     *
     * @param mixed estateCategories
     *
     * @return self
     */
    public function setEstateCategories($estateCategories)
    {
        $this->estateCategories = $estateCategories;

        return $this;
    }

    /**
     * Get the value of Price
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of Price
     *
     * @param mixed price
     *
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of Reference No
     *
     * @return mixed
     */
    public function getReferenceNo()
    {
        return $this->referenceNo;
    }

    /**
     * Set the value of Reference No
     *
     * @param mixed referenceNo
     *
     * @return self
     */
    public function setReferenceNo($referenceNo)
    {
        $this->referenceNo = $referenceNo;

        return $this;
    }

    /**
     * Get the value of Area
     *
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set the value of Area
     *
     * @param mixed area
     *
     * @return self
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get the value of Beds
     *
     * @return mixed
     */
    public function getBeds()
    {
        return $this->beds;
    }

    /**
     * Set the value of Beds
     *
     * @param mixed beds
     *
     * @return self
     */
    public function setBeds($beds)
    {
        $this->beds = $beds;

        return $this;
    }

    /**
     * Get the value of Baths
     *
     * @return mixed
     */
    public function getBaths()
    {
        return $this->baths;
    }

    /**
     * Set the value of Baths
     *
     * @param mixed baths
     *
     * @return self
     */
    public function setBaths($baths)
    {
        $this->baths = $baths;

        return $this;
    }

    /**
     * Get the value of Parking
     *
     * @return mixed
     */
    public function getParking()
    {
        return $this->parking;
    }

    /**
     * Set the value of Parking
     *
     * @param mixed parking
     *
     * @return self
     */
    public function setParking($parking)
    {
        $this->parking = $parking;

        return $this;
    }


    /**
     * Get the value of Currency
     *
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the value of Currency
     *
     * @param mixed currency
     *
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the value of Attributes
     *
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the value of Attributes
     *
     * @param mixed attributes
     *
     * @return self
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }


    /**
     * Get the value of Is Homepage
     *
     * @return mixed
     */
    public function getIsHomepage()
    {
        return $this->isHomepage;
    }

    /**
     * Set the value of Is Homepage
     *
     * @param mixed isHomepage
     *
     * @return self
     */
    public function setIsHomepage($isHomepage)
    {
        $this->isHomepage = $isHomepage;

        return $this;
    }


    /**
     * Get the value of Location
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the value of Location
     *
     * @param mixed location
     *
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }


    /**
     * Get the value of Latitude
     *
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the value of Latitude
     *
     * @param mixed latitude
     *
     * @return self
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get the value of Longitude
     *
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the value of Longitude
     *
     * @param mixed longitude
     *
     * @return self
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get the value of Related Estates
     *
     * @return mixed
     */
    public function getRelatedEstates()
    {
        return $this->relatedEstates;
    }

    /**
     * Set the value of Related Estates
     *
     * @param mixed relatedEstates
     *
     * @return self
     */
    public function setRelatedEstates($relatedEstates)
    {
        $this->relatedEstates = $relatedEstates;

        return $this;
    }


    /**
     * Get the value of Type
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of Type
     *
     * @param mixed type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }
}
