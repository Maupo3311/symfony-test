<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Basket;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @Route("/", name="basket")
     * @return Response
     */
    public function indexAction()
    {
        /** @var User $user */
        $user        = $this->getUser();
        $basketItems = $user->getBasketItems();

        return $this->render('basket/index.html.twig', [
            'basket_items' => $basketItems,
        ]);
    }

    /**
     * @Route("/delete-item/{id}", name="delete_basket_item")
     * @param Basket $basketItem
     * @return RedirectResponse
     */
    public function deleteBasketItemAction(Basket $basketItem)
    {
        if ($basketItem->getUser() === $this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($basketItem);
            $em->flush();

            $this->addFlash('success', 'Product removed from recycle bin');

            return $this->redirectToRoute('basket');
        } else {
            $this->addFlash('error', 'You are not allowed to empty this basket');

            return $this->redirectToRoute('basket');
        }
    }

    /**
     * @Route("/delete-items", name="delete_basket_items")
     * @return RedirectResponse
     */
    public function deleteBasketItemsAction()
    {
        /** @var User $user */
        $user        = $this->getUser();
        $basketItems = $user->getBasketItems();
        $em          = $this->getDoctrine()->getManager();

        foreach ($basketItems as $basketItem) {
            $em->remove($basketItem);
            $em->flush();
        }

        $this->addFlash('success', 'Recycle bin cleared');

        return $this->redirectToRoute('basket');
    }
}