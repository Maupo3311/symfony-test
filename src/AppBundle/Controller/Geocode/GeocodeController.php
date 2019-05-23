<?php

namespace AppBundle\Controller\Geocode;

use AppBundle\Services\GeocodeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Geocoder\Exception\Exception;

/**
 * Class GeocodeController
 * @Route("/geocode")
 * @package AppBundle\Controller\Geocode
 */
class GeocodeController extends Controller
{
    /**
     * @Route("/")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function indexAction(Request $request)
    {
        /** @var GeocodeService $geocdeService */
        $geocdeService = $this->get('app.geocode');

        $result = $geocdeService->startTest('178.219.184.0');

        return new Response('Your city - ' . $result->city);
    }
}