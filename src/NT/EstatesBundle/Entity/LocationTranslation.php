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

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 *  Entity holding Location's translations
 *
 * @ORM\Entity
 * @ORM\Table(name="locations_i18n", uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id"
 *   }), @ORM\UniqueConstraint(name="slug_unique_idx", columns={"locale", "title"})}
 * )
 * @Gedmo\Loggable
 *
 * @package NTEstatesBundle
 * @author  Nikolay Tumbalev <n.tumbalev@nt.bg>
 */
class LocationTranslation extends AbstractTranslation
{
    /**
     * @ORM\ManyToOne(targetEntity="NT\EstatesBundle\Entity\Location", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * Gets the value of Title.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the value of Title.
     *
     * @param mixed $title the Title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
