<?php

namespace AppBundle\Controller\Ajax;

use AppBundle\Entity\Feedback;
use AppBundle\Form\FeedbackType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FeedbackController
 * @Route("/ajax")
 * @package AppBundle\Controller\Ajax
 */
class FeedbackController extends BaseController
{
    /**
     * @Route("/feedback/submit", name="submit_feedback")
     */
    public function submitFeedbackAction(Request $request)
    {
        $data = $request->request->get('appbundle_feedback');

        $form = $this->createForm(FeedbackType::class);
        $form->handleRequest($request);
        $feedback = $form->getData();


//        if (!$message = $request->get('message')) {
//            return $this->errorResponse('Message cannot be empty', 400);
//        } else if (!$user = $this->getUser()) {
//            return $this->errorResponse('You are not logged in', 401);
//        }
//
//        /** @var EntityManager $em */
//        $em = $this->getDoctrine()->getManager();
//
//        $feedback = new Feedback();
//        $feedback->setUser($user)
//            ->setMessage($message);
//
//        $em->persist($feedback);
//        $em->flush();
//
//        return $this->successResponse('Feedback successfully sent');

        return $this->successResponse($data['message']);
    }
}