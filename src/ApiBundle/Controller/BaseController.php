<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\BrowserKit\Response;

/**
 * Class BaseController
 * @package ApiBundle\Controller
 */
class BaseController extends FOSRestController
{
    /**
     * Is called for a successful conclusion
     *
     * @param $message
     * @return Response
     */
    public function successResponse($message)
    {
        return new Response($message, 200);
    }

    /**
     * Called to output an error
     *
     * @param     $error
     * @param int $code
     * @return Response
     */
    public function errorResponse($error, int $code = 500)
    {
        return new Response($error, $code);
    }
}