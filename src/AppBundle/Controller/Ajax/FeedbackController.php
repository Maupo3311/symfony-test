<?php

namespace AppBundle\Controller\Ajax;

use EntityBundle\Entity\Feedback;
use EntityBundle\Entity\Image\FeedbackImage;
use AppBundle\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/feedback/submit", name="ajax_submit_feedback")
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function submitFeedbackAction(Request $request)
    {
        $images  = $request->files->get('appbundle_feedback')['images'];
        $message = $request->request->get('appbundle_feedback')['message'];

        if (!$message) {
            return $this->errorResponse('Message cannot be empty', 400);
        } elseif (!$user = $this->getUser()) {
            return $this->errorResponse('You are not logged in', 401);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $feedback = new Feedback();
        $feedback->setUser($user)
            ->setMessage($message);

        $feedbackImages = [];
        if ($images) {
            /** @var UploadedFile $image */
            foreach ($images as $image) {

                /** @var FeedbackImage $feedbackImage */
                $feedbackImage = new FeedbackImage();

                $feedbackImage->setFile($image)
                    ->uploadImage()
                    ->setFeedback($feedback);

                $em->persist($feedbackImage);
                $feedbackImages[] = $feedbackImage;
            }
        }

        $em->persist($feedback);
        $em->flush();

        return $this->successResponse('Feedback successfully sent');
    }

    /**
     * @Route("/feedback/delete", name="ajax_delete_feedback")
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteFeedback(Request $request)
    {
        if (!$id = $request->get('id')) {
            return $this->errorResponse('No feedback id specified', 401);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var FeedbackRepository $feedbackRepository */
        $feedbackRepository = $this->getDoctrine()->getRepository(Feedback::class);

        /** @var Feedback $feedback */
        if (!$feedback = $feedbackRepository->find($id)) {
            return $this->errorResponse('feedback does not exist', 404);
        }

        if ($this->getUser() !== $feedback->getUser()) {
            return $this->errorResponse('You can\'t remove this feedback', 403);
        }

        $em->remove($feedback);
        $em->flush();

        return $this->successResponse('Feedback successfully removed');
    }
}