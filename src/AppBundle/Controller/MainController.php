<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\FeedbackType;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Repository\FeedbackRepository;

/**
 * Class MainController
 * @package AppBundle\Controller
 */
class MainController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        /**@var ProductRepository $productRepository*/
        $productRepository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        $bestProducts = $productRepository->findBestProducts();

        return $this->render('main/index.html.twig', [
            'best_products' => $bestProducts,
        ]);
    }

    /**
     * Feedback page for bla....
     *
     * @Route("/feedback", name="feedback")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function feedbackAction(Request $request)
    {
        /** @var FeedbackRepository $feedbackRepository */
        $feedbackRepository = $this
            ->getDoctrine()
            ->getRepository(Feedback::class);

        $page = ($request->get('page')) ? $request->get('page') : 1;
        $theNumberOnThePage = 5;
        $allFeedbacks = $feedbackRepository->findByPage($page, $theNumberOnThePage);
        $quantityOfAllFeedbacks = $feedbackRepository->getTheQuantityOfAllFeedbacks();
        $numberOfPages = ceil($quantityOfAllFeedbacks / $theNumberOnThePage);

        $service  = $this->container->get('app.pagination');
        $position = $service->getHrefPosition($page, $numberOfPages);

        $form = $this->createForm(FeedbackType::class);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        /**@var User $user*/
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {

            if( !empty($user) ){
                /** @var Feedback $feedback */
                $feedback = $form->getData();
                $em       = $this->getDoctrine()->getManager();

                $feedback->setUser($user);
                $feedback->setName($user->getFirstName() . ' ' . $user->getLastName());
                $feedback->setEmail($user->getEmail());

                $em->persist($feedback);
                $em->flush();

                $this->addFlash('success', 'Saved!');
            } else {
                $this->addFlash('error', 'You can\'t send feedback to unauthorized people!');
            }

            return $this->redirectToRoute('feedback');
        }

        $formView = $form->createView();

        return $this->render('main/feedback.html.twig', [
            'formView'               => $formView,
            'allFeedbacks'           => $allFeedbacks,
            'page'                   => $page,
            'theNumberOnThePage'     => $theNumberOnThePage,
            'quantityOfAllFeedbacks' => $quantityOfAllFeedbacks,
            'numberOfPages'          => $numberOfPages,
            'position'               => $position,
        ]);
    }

    /**
     * @Route("feedback/delete/{id}", name="delete_feedback")
     * @param Feedback $feedback
     * @return RedirectResponse
     */
    public function deleteFeedback(Feedback $feedback)
    {
        /**@var User $user*/
        $user = $this->getUser();

        $em = $this
            ->getDoctrine()
            ->getManager();

        if( $feedback->getUser()->getId() == $user->getId() ){
            $em->remove($feedback);
            $em->flush();

            $this->addFlash('success', 'Your review was successfully removed!');

            return $this->redirectToRoute('feedback');
        } else {
            $this->addFlash('error', 'You cannot delete this review!');

            return $this->redirectToRoute('feedback');
        }
    }
}
