<?php

namespace AppBundle\Controller;

use EntityBundle\Entity\Comment;
use EntityBundle\Entity\Image\CommentImage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 *
 * @Route("/comment")
 * @package AppBundle\Controller
 */
class CommentController extends Controller
{
    /**
     * Deletes the comment
     *
     * @Route("/delete/{id}/{productId}", name="comment_delete")
     * @param Comment $comment
     * @param Int     $productId
     * @return RedirectResponse
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteCommentAction(Comment $comment, int $productId)
    {
        if ($this->getUser() === $comment->getUser()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $em->remove($comment);
            $em->flush();

            $this->addFlash('success', 'Comment successfully deleted');

            return $this->redirectToRoute('product_item', ['id' => $productId]);
        } else {
            $this->addFlash('error', 'You cannot delete this comment');

            return $this->redirectToRoute('product_item', ['id' => $productId]);
        }
    }
}