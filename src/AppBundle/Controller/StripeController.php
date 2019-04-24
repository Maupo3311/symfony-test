<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StripeController
 * @Route("/stripe")
 * @package AppBundle\Controller
 */
class StripeController extends Controller
{
    /**
     * @Route("/payment", name="stripe_payment")
     */
    public function paymentAction()
    {
        return $this->render('/stripe/payment.html.twig');
    }
}