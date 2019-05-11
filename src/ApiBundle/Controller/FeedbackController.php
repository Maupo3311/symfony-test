<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\User;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\FeedbackRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Hoa\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use FOS\RestBundle\View\View;

/**
 * Class FeedbackController
 * @Route("/api")
 * @package ApiBundle\Controller
 */
class FeedbackController extends BaseController
{
    /**
     * @Rest\Get("/feedback")
     * @SWG\Response(
     *     response=200,
     *     description="For stanadrt will return 10 feedbacks on 1 page,
     *          is governed by the parameters limit and page",
     *     @Model(type=Feedback::class)
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="Pagination page"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="Number of feddbacks per page"
     * )
     * @SWG\Tag(name="feedback")
     * @param Request $request
     * @return View|mixed
     */
    public function getAllAction(Request $request)
    {
        try {
            $page  = $request->get('page') ?: 1;
            $limit = $request->get('limit') ?: 10;

            /** @var FeedbackRepository $feedbackRepository */
            $feedbackRepository = $this->getDoctrine()->getRepository(Feedback::class);

            $restresult = $feedbackRepository->findByPage($page, $limit);

            if ($restresult === null) {
                return $this->errorResponse("feedback not found", 404);
            }
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode());
        }

        return $restresult;
    }

    /**
     * @Rest\get("/feedback/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the category with the specified id",
     *     @Model(type=Feedback::class)
     * )
     * @SWG\Tag(name="feedback")
     * @param int $id
     * @return View|object[]
     */
    public function getAction(int $id)
    {
        $restresult = $this->getDoctrine()->getRepository(Feedback::class)->find($id);
        if ($restresult === null) {
            return $this->errorResponse("there are no feedback exist", 404);
        }

        return $restresult;
    }

    /**
     * @Rest\Post("/feedback")
     * @SWG\Response(
     *     response=200,
     *     description="Object with success message",
     *     @Model(type=Feedback::class)
     * )
     * @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     type="integer",
     *     description="ID of the sender this feedback"
     * )
     * @SWG\Tag(name="feedback")
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository(User::class);

        /** @var User $user */
        if (!$user = $userRepository->find($request->get('user_id'))) {
            return $this->errorResponse('there are no user exist');
        }

        $feedback = new Feedback();
        $feedback
            ->setUser($user)
            ->setMessage($request->get('message'));

        $em->persist($feedback);
        $em->flush();

        return $this->successResponse('feedback added successfully');
    }

    /**
     * @Rest\Put("/feedback/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Object with a message what fields have been changed",
     *     @Model(type=Feedback::class)
     * )
     * @SWG\Parameter(
     *     name="message",
     *     in="query",
     *     type="string",
     *     description="Feedback message"
     * )
     * @SWG\Tag(name="feedback")
     * @param int     $id
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function putAction(int $id, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var FeedbackRepository $feedbackRepositoru */
        $feedbackRepository = $em->getRepository(Feedback::class);

        /** @var Feedback $feedback */
        if (!$feedback = $feedbackRepository->find($id)) {
            return $this->errorResponse('feedback not found', 404);
        }

        $feedback->setMessage($request->get('message'));

        $em->persist($feedback);
        $em->flush();

        return $this->successResponse('message successfully changed');
    }

    /**
     * @Rest\Delete("/feedback/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Object with success message",
     *     @Model(type=Feedback::class)
     * )
     * @SWG\Tag(name="feedback")
     * @param $id
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var FeedbackRepository $feedbackRepository */
        $feedbackRepository = $em->getRepository(Feedback::class);

        if (!$feedback = $feedbackRepository->find($id)) {
            return $this->errorResponse('feedback not found', 404);
        }

        $em->remove($feedback);
        $em->flush();

        return $this->successResponse('feedback successfully removed');
    }

    /**
     * @Rest\get("/feedback/{id}/user")
     * @SWG\Response(
     *     response=200,
     *     description="Return user this feedback",
     *     @Model(type=User::class)
     * )
     * @SWG\Tag(name="feedback")
     * @param int $id
     * @return User|Response
     */
    public function getUserAction(int $id)
    {
        /** @var FeedbackRepository $feedbackRepository */
        $feedbackRepository = $this->getDoctrine()->getRepository(Feedback::class);

        /** @var Feedback $feedback */
        if (!$feedback = $feedbackRepository->find($id)) {
            return $this->errorResponse('feedback not found', 404);
        }

        return $feedback->getUser();
    }
}