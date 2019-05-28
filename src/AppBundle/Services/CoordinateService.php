<?php

namespace AppBundle\Services;

use AppBundle\Entity\Shop;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CoordinateService
 * @package AppBundle\Services
 */
class CoordinateService
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * CoordinateService constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float
     */
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        // Calculate delta longitude and latitude.
        $delta_lat = ($lat2 - $lat1);
        $delta_lng = ($lng2 - $lng1);

        return round(6378137 * acos(cos($lat1) * cos($lat2) * cos($lng1 - $lng2) + sin($lat1) * sin($lat2)));
    }

    /**
     * @param Shop $shop
     * @return float
     */
    public function getDistanceToShop(Shop $shop)
    {
        $userLocation = $this->session->get('userLocation');

        return $this->getDistance(
            $userLocation->getLatitude(),
            $userLocation->getLongitude(),
            $shop->getLat(),
            $shop->getLon()
        );
    }

    /**
     * @param Shop $shop
     * @param      $distance
     * @return bool
     */
    public function ShopWithinAGivenDistance(Shop $shop, $distance)
    {
        return $this->getDistanceToShop($shop) < $distance ? true : false;
    }

    /**
     * @param array $allShops
     * @param       $distance
     * @return array
     */
    public function GetSuitableShopCategories(array $allShops, $distance)
    {
        $suitableShops = [];

        foreach ($allShops as $shop) {
            if ($this->ShopWithinAGivenDistance($shop, $distance)) {
                $suitableShops[] = $shop;
            }
        }

        $suitableCategories = [];

        /** @var Shop $shop */
        foreach ($suitableShops as $shop) {
            foreach ($shop->getCategories() as $category) {
                $suitableCategories[] = $category;
            }
        }

        return $suitableCategories;
    }
}