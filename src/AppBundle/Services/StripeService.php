<?php

namespace AppBundle\Services;

use AppBundle\Entity\Basket;
use Stripe\Charge;
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
     * @param Basket $basketItem
     */
    public function createSession(Basket $basketItem)
    {
        Session::create([
            'customer_email' => $basketItem->getUser()->getEmail(),
            'success_url' => 'http://127.0.0.1:8000/basket/',
            'cancel_url' => 'http://127.0.0.1:8000/basket/',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'amount' => round($basketItem->getBasketProduct()->getPrice()),
                'currency' => 'usd',
                'name' => $basketItem->getBasketProduct()->getTitle(),
                'description' => $basketItem->getBasketProduct()->getDescription(),
                'images' => ['https://www.example.com/t-shirt.png'],
                'quantity' => 1,
            ]]
        ]);
    }

    /**
     * @param Basket $basketItem
     */
    public function createObject(Basket $basketItem)
    {
        Charge::create([
                "amount" => round($basketItem->getBasketProduct()->getPrice()),
                "currency" => "usd",
                'source' => 'tok_visa',
                'receipt_email' => $basketItem->getUser()->getEmail(),
        ]);
    }
}