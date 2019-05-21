<?php

namespace AppBundle\Controller\Ajax;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function errorResponse($message, $code = 500)
    {
        return new Response($message, $code);
    }

    /**
     * @param $message
     * @return Response
     */
    public function successResponse($message)
    {
        return new Response($message, 200);
    }
}