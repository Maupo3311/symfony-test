<?php

namespace ApiBundle\Controller;

use EntityBundle\Entity\Comment;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use Symfony\Component\BrowserKit\Response;

/**
 * Class BaseController
 * @package ApiBundle\Controller
 */
abstract class BaseController extends FOSRestController
{
    /**
     * Is called for a successful conclusion
     *
     * @param $message
     * @return Response
     */
    protected function successResponse($message)
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
    protected function errorResponse($error, int $code = 500)
    {
        return new Response($error, $code);
    }

    /**
     * @param        $data
     * @param string $format
     * @return mixed
     */
    protected function serialize($data, $format = 'json')
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true)
            ->setGroups(['Default']);

        $serializer = $this->container->get('jms_serializer');

        return $serializer->serialize($data, $format, $context);
    }
}