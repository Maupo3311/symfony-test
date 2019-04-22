<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Basket;
use AppBundle\Entity\User;
use AppBundle\Repository\BasketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BasketController
 * @package AppBundle\Controller
 * @Route("/basket")
 */
class BasketController extends Controller
{
    /**
     * @Route("/")
     * @return Response
     */
    public function indexAction()
    {
//        /** @var User $user */
//        $user = $this->getUser();
//        /** @var Basket $basket */
//        $basket   = $user->getBasket();
//        $products = $basket->getProducts();
        /** @var BasketRepository $basketRepository */
        $em = $this->getDoctrine()->getManager();
        $basketRepository = $this->getDoctrine()->getRepository(Basket::class);

        $baskets = $basketRepository->findAll();
        $products = [];

        return $this->render('basket/index.html.twig', [
            'products' => $products,
            'baskets' => $baskets,
        ]);
    }
}