<?php

namespace AppBundle\Controller\Ajax;

use AppBundle\Services\SerializerService;
use EntityBundle\Entity\Comment;
use EntityBundle\Entity\Image\CommentImage;
use EntityBundle\Entity\Product;
use EntityBundle\Repository\CommentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 * @Route("/ajax")
 * @package AppBundle\Controller\Ajax
 */
class CommentController extends BaseController
{
    /**
     * @Route("/comment/get-by-product/{id}", name="ajax_get_comments_by_product")
     * @param Product $product
     * @return string
     */
    public function getCommentsAction(Product $product)
    {
        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);

        /** @var SerializerService $serializeService */
        $serializeService = $this->getSerializerService();

        $serializeComments = $serializeService
            ->serializeComments($commentRepository->findByProduct($product));

        return $this->jsonResponse($serializeComments);
    }

    /**
     * @Route("/comment/submit", name="ajax_submit_comment")
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function submitCommentAction(Request $request)
    {
        if (!$productId = $request->get('productId')) {
            return $this->errorResponse('productId cannot be empty', 400);
        };
        $images  = $request->files->get('appbundle_comment')['images'];
        $message = $request->request->get('appbundle_comment')['message'];

        if (!$message) {
            return $this->errorResponse('Message cannot be empty', 400);
        } elseif (!$user = $this->getUser()) {
            return $this->errorResponse('You are not logged in', 401);
        }

        $productRepository = $this->getDoctrine()->getRepository(Product::class);

        /** @var Product $product */
        if (!$product = $productRepository->find($productId)) {
            return $this->errorResponse('Product not found', 404);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $comment = new Comment();
        $comment->setUser($user)
            ->setMessage($message)
            ->setProduct($product);

        $commentImages = [];
        if ($images) {
            /** @var UploadedFile $image */
            foreach ($images as $image) {

                /** @var CommentImage $commentImage */
                $commentImage = new CommentImage();

                $commentImage->setFile($image)
                    ->uploadImage()
                    ->setComment($comment);

                $em->persist($commentImage);
                $commentImages[] = $commentImage;
            }
        }

        $em->persist($comment);
        $em->flush();

        return $this->successResponse('Comment successfully sent');
    }

    /**
     * @Route("/comment/delete", name="ajax_delete_comment")
     * @param Request $request
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteComment(Request $request)
    {
        if (!$id = $request->get('id')) {
            return $this->errorResponse('No comment id specified', 401);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var CommentRepository $commentRepository */
        $commentRepository = $this->getDoctrine()->getRepository(Comment::class);

        /** @var Comment $comment */
        if (!$comment = $commentRepository->find($id)) {
            return $this->errorResponse('Comment does not exist', 404);
        }

        if ($this->getUser() !== $comment->getUser()) {
            return $this->errorResponse('You can\'t remove this comment', 403);
        }

        $em->remove($comment);
        $em->flush();

        return $this->successResponse('Comment successfully removed');
    }
}