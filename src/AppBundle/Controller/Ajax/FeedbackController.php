<?php

namespace AppBundle\Controller\Ajax;

use AppBundle\Entity\Feedback;
use AppBundle\Form\FeedbackType;
use AppBundle\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class FeedbackController
 * @Route("/ajax")
 * @package AppBundle\Controller\Ajax
 */
class FeedbackController extends BaseController
{
    /**
     * @Route("/feedback/submit", name="submit_feedback")
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function submitFeedbackAction(Request $request)
    {
        $data = $request->request->get('appbundle_feedback');

        if (!$message = $data['message']) {
            return $this->errorResponse('Message cannot be empty', 400);
        } else if (!$user = $this->getUser()) {
            return $this->errorResponse('You are not logged in', 401);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $feedback = new Feedback();
        $feedback->setUser($user)
            ->setMessage($message);

        $em->persist($feedback);
        $em->flush();

        return $this->successResponse('Feedback successfully sent');
    }

    /**
     * @Rest\Get("/feedback/load", name="load_feedback")
     * @param Request $request
     * @return mixed
     */
    public function loadFeedbackAction(Request $request)
    {
        $page = ($request->get('page')) ?: 1;
        $theNumberOnThePage = ($request->get('theNumberOnThePage')) ?: 5;

        /** @var FeedbackRepository $feedbackRepository */
        $feedbackRepository = $this->getDoctrine()->getRepository(Feedback::class);

        dump($feedbackRepository->findByPage($page, $theNumberOnThePage));
        return $this->successResponse(\GuzzleHttp\json_encode($feedbackRepository->findByPage($page, $theNumberOnThePage)));
    }
}