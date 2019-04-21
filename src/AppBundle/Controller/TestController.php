<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TestController
 * @Route("/test")
 * @package AppBundle\Controller
 */
class TestController extends Controller
{
    /**
     * @Route("/")
     * @return Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();

       $message = Swift_Message::newInstance()
           ->setSubject('Test Swift_Message')
           ->setFrom( $this->getParameter('mailer_user') )
           ->setTo( $user->getEmail() )
           ->setBody('Test message');


       $mailer = $this->get('mailer');

       $mailer->send($message);

       return new Response('The letter was sent');
    }
}