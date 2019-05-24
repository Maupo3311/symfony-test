<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Shop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShopController
 * @Route("/shop")
 * @package AppBundle\Controller
 */
class ShopController extends Controller
{
    /**
     * @Route("/show/{id}", name="show_shop_by_id")
     * @param Shop $shop
     * @return Response
     */
    public function showAction(Shop $shop)
    {
        return $this->render('/shop/show.html.twig', [
           'shop' => $shop,
        ]);
    }
}