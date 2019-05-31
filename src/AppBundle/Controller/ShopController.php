<?php

namespace AppBundle\Controller;

use EntityBundle\Entity\Shop;
use AppBundle\Services\locationiqService;
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
        $geocode = new locationiqService($this->getParameter('locationiq_geocode_api_key'));

        $geocodeData = $geocode->getDataByCoords($shop->getLat(), $shop->getLon());

        return $this->render('/shop/show.html.twig', [
            'shop'         => $shop,
            'geocode'      => $geocode,
            'geocode_data' => $geocodeData,
        ]);
    }
}