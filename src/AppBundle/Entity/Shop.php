<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shop
 *
 * @ORM\Table(name="shop")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShopRepository")
 */
class Shop
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
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=255,
     *     unique=true
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="description",
     *     type="text",
     *     nullable=true
     * )
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(
     *     name="phone_number",
     *     type="string",
     * )
     */
    private $phoneNumber;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Category",
     *     mappedBy="shop",
     *     cascade={"persist","remove"},
     *     orphanRemoval=true,
     * )
     */
    private $categories;

    /**
     * @var ArrayCollection $images
     *
     * @ORM\OneToMany(
     *     targetEntity="AppBundle\Entity\Image\ShopImage",
     *      cascade={"persist","remove"},
     *      orphanRemoval=true,
     *      mappedBy="shop"
     * )
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="images_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $images;

    /**
     * Shop constructor.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->images     = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection $images
     * @return $this
     */
    public function setImages(ArrayCollection $images)
    {
        $this->images = $images;

        return $this;
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
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param ArrayCollection $categories
     * @return $this
     */
    public function setCategories(ArrayCollection $categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int|void
     */
    public function getQuantityProducts()
    {
        $finalCount = 0;

        /** @var Category $category */
        foreach ($this->categories as $category) {
            $finalCount += $category->getQuantityProducts();
        }

        return $finalCount;
    }
}
