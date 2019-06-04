<?php

namespace ApiBundle\Controller;

use EntityBundle\Entity\Comment;
use EntityBundle\Entity\Product;
use EntityBundle\Entity\User;
use EntityBundle\Repository\CommentRepository;
use EntityBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Swagger\Annotations as SWG;

/**
 * Class CommentController
 * @Route("/api")
 * @package ApiBundle\Controller
 */
class CommentController extends BaseController
{
    /**
     * @Rest\Get("/comment-list")
     * @SWG\Response(
     *     response=200,
     *     description="For stanadrt will return 10 comments on 1 page,
     *          is governed by the parameters limit and page",
     *     @Model(type=Product::class)
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
     *     description="Number of comments per page"
     * )
     * @SWG\Tag(name="comment")
     * @param Request $request
     * @return View|mixed
     */
    public function getAllAction(Request $request)
    {
        try {
            $page  = $request->get('page') ?: 1;
            $limit = $request->get('limit') ?: 10;

            /** @var CommentRepository $commentRepository */
            $commentRepository = $this->getDoctrine()->getRepository(Comment::class);

            if (!$restresult = $commentRepository->findByPage($page, $limit)) {
                return $this->errorResponse("comment not found", 404);
            }
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode());
        }

        return $restresult;
    }

    /**
     * @Rest\get("/comment/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the comment with the specified id",
     *     @Model(type=Comment::class)
     * )
     * @SWG\Tag(name="comment")
     * @param int $id
     * @return View|object[]
     */
    public function getAction(int $id)
    {
        $restresult = $this->getDoctrine()->getRepository(comment::class)->find($id);
        if ($restresult === null) {
            return $this->errorResponse("there are no comment exist", 404);
        }

        return $restresult;
    }

    /**
     * @Rest\Post("/comment")
     * @SWG\Response(
     *     response=200,
     *     description="Object with success message",
     *     @Model(type=Comment::class)
     * )
     * @SWG\Parameter(
     *     name="product_id",
     *     in="query",
     *     type="integer",
     *     description="product ID to which the comment is attached"
     * )
     * @SWG\Parameter(
     *     name="message",
     *     in="query",
     *     type="string",
     *     description="Text comment"
     * )
     * @SWG\Tag(name="comment")
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postAction(Request $request)
    {
        /** @var User $user */
        if (!$user = $this->getUser()) {
            return $this->errorResponse('You are not logged in', 401);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var ProductRepository $productRepository */
        $productRepository = $this->getDoctrine()->getRepository(Product::class);

        /** @var Product $product */
        $product = $productRepository->find($request->get('product_id'));

        $comment = new Comment();
        $comment
            ->setUser($user)
            ->setMessage($request->get('message'))
            ->setProduct($product);

        $em->persist($comment);
        $em->flush();

        return $this->successResponse('Comment Added Successfully');
    }

    /**
     * @Rest\Put("/comment/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Object with a message what fields have been changed",
     *     @Model(type=Comment::class)
     * )
     * @SWG\Parameter(
     *     name="message",
     *     in="query",
     *     type="string",
     *     description="Text comment"
     * )
     * @SWG\Tag(name="comment")
     * @param int     $id
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function putAction(int $id, Request $request)
    {
        /** @var User $user */
        if (!$user = $this->getUser()) {
            return $this->errorResponse('You are not logged in', 401);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);

        /** @var Comment $comment */
        if (!$comment = $commentRepository->find($id)) {
            return $this->errorResponse('comment not found', 404);
        }

        if ($comment->getUser() !== $user) {
            return $this->errorResponse('you cannot change this comment', 403);
        }

        $comment->setMessage($request->get('message'));

        $em->persist($comment);
        $em->flush();

        return $this->successResponse('Comment successfully change');
    }

    /**
     * @Rest\Delete("/comment/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Object with success message",
     *     @Model(type=Product::class)
     * )
     * @SWG\Tag(name="comment")
     * @param $id
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAction($id)
    {
        /** @var User $user */
        if (!$user = $this->getUser()) {
            return $this->errorResponse('You are not logged in', 401);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);

        /** @var Comment $comment */
        if (!$comment = $commentRepository->find($id)) {
            return $this->errorResponse('Comment Not Found', 404);
        }

        if ($comment->getUser() !== $user) {
            return $this->errorResponse('you cannot delete this comment', 403);
        }

        $em->remove($comment);
        $em->flush();

        return $this->successResponse('Comment successfully removed');
    }
}