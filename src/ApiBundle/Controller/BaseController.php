<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\BrowserKit\Response;

class BaseController extends FOSRestController
{
    /**
     * @param $message
     * @return Response
     */
    public function successResponse($message)
    {
        return new Response($message, 200);
    }

    /**
     * @param     $error
     * @param int $code
     * @return Response
     */
    public function errorResponse($error, int $code = 500)
    {
        return new Response($error, $code);
    }
}