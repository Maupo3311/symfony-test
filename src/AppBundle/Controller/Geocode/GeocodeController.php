<?php

namespace AppBundle\Controller\Geocode;

use Geocoder\Query\GeocodeQuery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GeocodeController
 * @Route("/geocode")
 * @package AppBundle\Controller\Geocode
 */
class GeocodeController extends Controller
{
    /**
     * @Route("/")
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $result = $this->container
            ->get('bazinga_geocoder.provider.acme')
            ->geocodeQuery(GeocodeQuery::create('address'));

        $body = $this->container
            ->get('Geocoder\Dumper\GeoJson')
            ->dump($result);

        $response = new Response();
        $response->setContent($body);

        return $response;
    }
}