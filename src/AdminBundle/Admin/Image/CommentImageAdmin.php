<?php

namespace AdminBundle\Admin\Image;

use EntityBundle\Entity\Comment;
use EntityBundle\Entity\Image\CommentImage;
use EntityBundle\Entity\Image\ProductImage;
use EntityBundle\Entity\Product;
use EntityBundle\Repository\CommentRepository;
use EntityBundle\Repository\ProductRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelHiddenType;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class ProductImageAdmin
 * @package AdminBundle\Admin\Image
 */
class CommentImageAdmin extends AbstractAdmin
{
    /**
     * @return ProductImage|mixed
     */
    public function getNewInstance()
    {
        /** @var CommentImage $instance */
        $instance = parent::getNewInstance();

        if (!$comment = $instance->getComment()) {
            $refererUrl = $this->getRequest()->headers->get('referer');
            $commentId  = basename(str_replace('/edit', '', $refererUrl));

            if ($commentId) {
                /** @var ModelManager $modelManager */
                $modelManager = $this->modelManager;

                /** @var CommentRepository $commentRepository */
                $commentRepository = $modelManager
                    ->getEntityManager(Comment::class)
                    ->getRepository(Comment::class);

                $comment = $commentRepository->find($commentId);

            }
        }

        $instance->setComment($comment);

        return $instance;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var CommentImage $subject */
        $subject = $this->getSubject();

        $fileFieldOptions = ['required' => false];

        if ($subject && $webPath = $subject->getImageWebPath()) {
            $fullPath = '/../../../../' . $webPath;

            $fileFieldOptions['attr'] = ['hidden' => true];

            $fileFieldOptions['help'] = '
            <a href="' . $fullPath . '" target="_blank">
                <img 
                    alt="' . $subject->getFilePath() . '" 
                    title="' . $subject->getFilePath() . '"
                    src="' . $fullPath . '" 
                    class="admin-preview" style="width:400px" 
                /> 
            </a>';
        }

        $formMapper
            ->add('comment', ModelHiddenType::class)
            ->add('file', FileType::class, ['label' => false], $fileFieldOptions);
    }

    /**
     * @param CommentImage $object
     * @return string
     */
    public function toString($object)
    {
        return $object instanceof CommentImage
            ? $object->getCommentId()
            : 'CommentImage';
    }
}