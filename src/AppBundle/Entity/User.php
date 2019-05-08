<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Stripe\Customer;
use Stripe\Stripe;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_id", type="string", length = 255, nullable = true)
     */
    private $customerId;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=32)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=32)
     */
    private $lastName;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="Feedback", mappedBy="user")
     */
    private $feedbacks;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Basket",
     *     mappedBy="user",
     *     )
     */
    private $basketItems;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string")
     */
    private $apiKey;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Comment",
     *     mappedBy="user"
     *     )
     */
    private $comments;

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return $this
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param string $chargeId
     * @return $this
     */
    public function setCustomerId(string $chargeId)
    {
        $this->customerId = $chargeId;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $comments
     * @return User
     */
    public function setComments(ArrayCollection $comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSubscription()
    {
        Stripe::setApiKey('sk_test_WyBgvLS9GfLuCLKCp2Q5Cngc00XGV4hWVX');

        if ($this->getCustomerId()) {
            $customer = Customer::retrieve($this->getCustomerId());

            foreach ($customer->subscriptions as $subscription) {
                return $subscription->plan->id;
            }
        }

        return false;
    }

    /**
     * @return float|int
     */
    public function getCoefficient()
    {
        switch ($this->getSubscription()){
            case 'bronze':
                return 0.9;
            case 'silver':
                return 0.85;
            case 'gold':
                return 0.8;
            default:
                return 1;
        }
    }

    /**
     * @return Collection
     */
    public function getFeedbacks()
    {
        return $this->feedbacks;
    }

    /**
     * @param Collection $feedbacks
     * @return User
     */
    public function setFeedbacks(Collection $feedbacks)
    {
        $this->feedbacks = $feedbacks;

        return $this;
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->feedbacks   = new ArrayCollection();
        $this->basketItems = new ArrayCollection();
        $this->comments    = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getBasketItems()
    {
        return $this->basketItems;
    }

    /**
     * @param ArrayCollection $basketItems
     * @return User
     */
    public function setBasketItems(ArrayCollection $basketItems)
    {
        $this->basketItems = $basketItems;

        return $this;
    }

    /**
     * @return array
     */
    public function getBasketProducts()
    {
        $basketProducts = [];

        /** @var Basket $basketItem */
        foreach ($this->getBasketItems() as $basketItem) {
            $basketProducts[] = $basketItem->getBasketProduct();
        }

        return $basketProducts;
    }

    /**
     * @return int
     */
    public function getTotalPriceBasketProducts()
    {
        $totalPrice = 0;

        /** @var Basket $basketItem */
        foreach ($this->getBasketItems() as $basketItem) {
            $totalPrice += $basketItem->getBasketProduct()->getPrice() * $basketItem->getNumberOfProducts();
        }

        return $totalPrice;
    }

    /**
     * @return int
     */
    public function getFinalPriceBasketProducts()
    {
        return round($this->getTotalPriceBasketProducts() * $this->getCoefficient());
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
     * Set login.
     *
     * @param string $login
     *
     * @return User
     */

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}
