<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Services\StripeService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Stripe\Customer;
use Stripe\Error\Api;
use Stripe\Plan;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SubscriptionController
 *
 * @Route("/subscription")
 * @package AppBundle\Controller
 */
class SubscriptionController extends Controller
{
    /**
     * @Route("/", name="subscription")
     * @param Request $request
     * @return RedirectResponse|Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Api
     */
    public function indexAction(Request $request)
    {
        /** @var StripeService $stripeService */
        $stripeService = $this->get('app.stripe');

        /** @var User $user */
        $user = $this->getUser();

        $token = $request->request->get('stripeToken');
        if ($token) {
            if ($user->getCustomerId()) {
                $customer = $stripeService->getCustomer($user->getCustomerId());
            } else {
                /** @var EntityManager $em */
                $em = $this->getDoctrine()->getManager();

                $customer = $stripeService->createCustomer([
                    'email'  => $user->getEmail(),
                    'source' => $token,
                ]);

                $user->setCustomerId($customer->id);
                $em->persist($user);
                $em->flush();
            }

            if (!empty($customer->subscriptions->data)) {
                $this->addFlash('error', 'You already have a subscription');

                return $this->redirectToRoute('subscription');
            } else if (!$planId = $request->request->get('plan')) {
                $this->addFlash('error', 'Error');

                return $this->redirectToRoute('subscription');
            }

            /** @var Plan $plan */
            $plan = $stripeService->getPlan($planId);

            $stripeService->createSubscription($customer, $plan);
            $this->addFlash('success', 'You have successfully purchased a subscription');

            $this->redirectToRoute('homepage');
        }

        return $this->render('subscription/index.html.twig', [
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'all_plans'         => $stripeService->getAllPlans(),
        ]);
    }

    /**
     * @Route("/delete", name="delete_subscription")
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteSubscription(Request $request)
    {
        $plan = $request->request->get('plan');
        if(!$plan){
            return $this->redirectToRoute('subscription');
        }

        /** @var StripeService $stripeService */
        $stripeService = $this->get('app.stripe');

        /** @var User $user */
        $user = $this->getUser();

        if(!$user->getCustomerId()){
            $this->addFlash('error', 'You have no subscriptions');

            return $this->redirectToRoute('subscription');
        }

        /** @var Customer $customer */
        $customer = $stripeService->getCustomer($user->getCustomerId());

        foreach ($customer->subscriptions as $subscription) {
            if($subscription->plan->id === $plan){
                $stripeService->deleteSubscription($subscription->id);
                $this->addFlash('success', 'You have successfully canceled your subscription');

                return $this->redirectToRoute('subscription');
            }
        }

        return $this->redirectToRoute('subscription');
    }
}