<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Class UserController
 * @Route("/api")
 * @package ApiBundle\Controller
 */
class UserController extends FOSRestController
{
    /**
     * @Rest\Get("/user/get-me")
     * @return View|mixed
     */
    public function getMeAction()
    {
        return $this->get('security.token_storage')->getToken()->getUser();
    }

    public function loginAction()
    {
        try{
            $token = $this->get('security.authentication.manager')->authenticate(new UsernamePasswordToken('username', 'password', 'firewall'));
            $this->get('security.context')->setToken($token);
        }
        catch(BadCredentialsException $e){
            return new View("Bad", Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
        }
        return new View("Success", Response::HTTP_NON_AUTHORITATIVE_INFORMATION);
    }
}