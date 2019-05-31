<?php

namespace AppBundle\Controller;

use EntityBundle\Entity\Basket;
use EntityBundle\Entity\User;
use AppBundle\Services\StripeService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Stripe\ApiResource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * Shows the user's basket
     *
     * @Route("/", name="basket")
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function indexAction(Request $request)
    {
        /** @var User $user */
        $user        = $this->getUser();
        $basketItems = $user->getBasketItems();

        /** @var StripeService $stipeService */
        $stipeService = $this->get('app.stripe');

        /**
         *  If the payment button has been pressed
         */
        if ($token = $request->request->get('stripeToken')) {
            $userEmail = ($request->request->get('stripeEmail')) ?: $user->getEmail();

            /** @var ApiResource $charge */
            $charge = $stipeService->createCharge($token, $user->getFinalPriceBasketProducts(), $userEmail);

            $sellerMessage = $charge->outcome->seller_message;
            if ($sellerMessage === 'Payment complete.') {
                $this->addFlash('success', $sellerMessage);

                /** @var EntityManager $em */
                $em = $this->getDoctrine()->getManager();

                /** @var Basket $basketItem */
                foreach ($basketItems as $basketItem) {
                    $currentNumber = $basketItem->getBasketProduct()->getNumber();
                    $newNumber     = $currentNumber - $basketItem->getNumberOfProducts();

                    $basketItem->getBasketProduct()->setNumber($newNumber);

                    $em->remove($basketItem);
                    $em->flush();
                }

                return $this->redirectToRoute('basket');
            } else {
                $this->addFlash('error', $sellerMessage);
            }
        }

        return $this->render('basket/index.html.twig', [
            'basket_items'      => $basketItems,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    /**
     * Removes the product item from the basket
     *
     * @Route("/delete-item/{id}", name="delete_basket_item")
     * @param Basket $basketItem
     * @return RedirectResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteBasketItemAction(Basket $basketItem)
    {
        if ($basketItem->getUser() === $this->getUser()) {
            /** @var EntityManager $em */
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
     * Completely clears the basket
     *
     * @Route("/delete-items", name="delete_basket_items")
     * @return RedirectResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteBasketItemsAction()
    {
        /** @var User $user */
        $user        = $this->getUser();
        $basketItems = $user->getBasketItems();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        foreach ($basketItems as $basketItem) {
            $em->remove($basketItem);
            $em->flush();
        }

        $this->addFlash('success', 'Recycle bin cleared');

        return $this->redirectToRoute('basket');
    }
}