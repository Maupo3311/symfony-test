<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Basket
 *
 * @ORM\Table(name="basket")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BasketRepository")
 */
class Basket
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="basketItems")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="basketItems")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     */
    private $basketProduct;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_of_products", type="integer", nullable=false)
     */
    private $numberOfProducts;

    /**
     * @return float|int
     */
    public function getTotalPriceAllProducts()
    {
        return $this->getBasketProduct()->getPrice() * $this->getNumberOfProducts();
    }

    /**
     * @return int
     */
    public function getNumberOfProducts()
    {
        return $this->numberOfProducts;
    }

    /**
     * @param int $numberOfProducts
     */
    public function setNumberOfProducts(int $numberOfProducts)
    {
        $this->numberOfProducts = $numberOfProducts;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Basket
     */
    public function setUser(User $user): Basket
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Product
     */
    public function getBasketProduct(): Product
    {
        return $this->basketProduct;
    }

    /**
     * @param Product $basketProduct
     * @return Basket
     */
    public function setBasketProduct(Product $basketProduct): Basket
    {
        $this->basketProduct = $basketProduct;

        return $this;
    }

}
