<?php

namespace AppBundle\Entity\Image;

use AppBundle\Entity\Comment;
use AppBundle\Enum\ImageType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CommentImage
 *
 * @ORM\Table(name="comment_image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentImageRepository")
 */
class CommentImage extends AbstractImage
{
    /**
     * @var Comment
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment", inversedBy="images")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_id", referencedColumnName="id", nullable=false)
     * })
     */
    protected $comment;

    /***********************************************
     *                Virtual fields
     ***********************************************/

    /**
     * Comment Id
     *
     * @return null|int
     */
    public function getCommentId()
    {
        return $this->getComment()->getId();
    }

    /***********************************************/

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return ImageType::FEEDBACK;
    }

    /**
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function setComment(Comment $comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
