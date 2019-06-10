<?php

namespace AppBundle\Controller\Ajax;

use AppBundle\Services\SerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController
 * @package AppBundle\Controller\Ajax
 */
class BaseController extends Controller
{
    /**
     * @param     $message
     * @param int $code
     * @return Response
     */
    protected function errorResponse($message, $code = 500)
    {
        return new Response($message, $code);
    }

    /**
     * @param $message
     * @return Response
     */
    protected function successResponse($message)
    {
        return new Response($message, 200);
    }

    /**
     * @param $object
     * @return string
     */
    protected function jsonResponse($object)
    {
        return $this->json($object);
    }

    /**
     * @return SerializerService
     */
    protected function getSerializerService()
    {
        /** @var SerializerService $serializerService */
        return $this->get('app.serializer_service');
    }
}