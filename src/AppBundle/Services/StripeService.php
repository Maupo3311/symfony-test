<?php

namespace AppBundle\Services;

use AppBundle\Entity\Basket;
use Stripe\ApiResource;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Checkout\Session;

/**
 * Class StripeService
 * @package AppBundle\Services
 */
class StripeService
{
    /**
     * @param string $key
     * @return $this
     */
    public function setApiKey(string $key)
    {
        Stripe::setApiKey($key);

        return $this;
    }

    /**
     * @param string $token
     * @param int    $totalPrice
     * @param string $userEmail
     * @return ApiResource
     */
    public function createCharge(string $token ,int $totalPrice, string $userEmail)
    {
        return Charge::create([
            "amount"        => $totalPrice * 100,
            "currency"      => "usd",
            'source'        => $token,
            'receipt_email' => $userEmail,
        ]);
    }

    public function createSource(ApiResource $customer)
    {
        return Customer::createSource($customer->id, [
                'source' => 'tok_visa',
            ]
        );
    }

    /**
     * @param array $data
     * @return ApiResource
     */
    public function createCustomer(array $data)
    {
        return Customer::create($data);
    }
}