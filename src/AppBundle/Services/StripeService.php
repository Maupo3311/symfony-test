<?php

namespace AppBundle\Services;

use AppBundle\Entity\Basket;
use Stripe\ApiResource;
use Stripe\Charge;
use Stripe\Collection;
use Stripe\Customer;
use Stripe\Error\Api;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\StripeObject;
use Stripe\Subscription;

/**
 * Class StripeService
 * @package AppBundle\Services
 */
class StripeService
{

    /**
     * @var string
     */
    protected $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;

        Stripe::setApiKey($this->apiKey);
    }

    /**
     * @param string $token
     * @param int    $totalPrice
     * @param string $userEmail
     * @return ApiResource
     */
    public function createCharge(string $token, int $totalPrice, string $userEmail)
    {
        return Charge::create([
            "amount"        => $totalPrice * 100,
            "currency"      => "usd",
            'source'        => $token,
            'receipt_email' => $userEmail,
        ]);
    }

    /**
     * @return Collection
     * @throws Api
     */
    public function getAllPlans()
    {
        return Plan::all();
    }

    /**
     * @param string $id
     * @return StripeObject
     */
    public function getPlan(string $id)
    {
        return Plan::retrieve($id);
    }

    /**
     * @param StripeObject $customer
     * @param Plan         $plan
     * @return ApiResource
     */
    public function createSubscription(StripeObject $customer, Plan $plan)
    {
        return Subscription::create([
            'customer'  => $customer->id,
            'items'     => [
                ['plan' => $plan->id],
            ],
        ]);
    }

    public function deleteSubscription($id)
    {
        /** @var Subscription $sub */
        $sub = Subscription::retrieve($id);
        $sub->cancel();
    }

    /**
     * @param array $data
     * @return ApiResource
     */
    public function createCustomer(array $data)
    {
        return Customer::create($data);
    }

    /**
     * @param string $id
     * @return StripeObject
     */
    public function getCustomer(string $id)
    {
        return Customer::retrieve($id);
    }
}