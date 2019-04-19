<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\Image\FeedbackImage;
use AppBundle\Entity\User;
use AppBundle\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class FeedbackAdmin
 * @package AdminBundle\Admin
 * @ORM\HasLifecycleCallbacks()
 */
final class FeedbackAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     *
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user', EntityType::class, [
                'class'        => User::class,
                'choice_label' => 'username',
            ])
            ->add('name')
            ->add('email')
            ->add('message', TextareaType::class)
            ->add('created')
            ->add('images', CollectionType::class, [
                'required' => false,
            ], [
                'edit' => 'inline',
                'sortable' => 'position',
            ]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('message')
            ->addIdentifier('created');
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        /**@var Feedback $object */
        if ($object->getImages()) {
            /** @var FeedbackImage $attachedFile */
            foreach ($object->getImages() as $attachedFile) {
                $attachedFile->setCreatedAt(new \DateTime());
            }
        }
    }

    /**
     * Takes an image from a one-time feedback
     *
     * @param Feedback $object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postPersist($object)
    {
        /** @var ModelManager $modelManager */
        $modelManager = $this->modelManager;

        /** @var EntityManager $em */
        $feedbackManager         = $modelManager->getEntityManager(Feedback::class);
        $FeedbackImageRepository = $modelManager
            ->getEntityManager(FeedbackImage::class)
            ->getRepository(FeedbackImage::class);

        /** @var FeedbackRepository $FeedbackRepository */
        $FeedbackRepository = $feedbackManager
            ->getRepository(Feedback::class);

        $uploadFeedbacks = $FeedbackRepository->findUploadFeedback();
        /** @var Feedback $uploadFeedback */
        foreach ($uploadFeedbacks as $uploadFeedback) {
            /** @var FeedbackImage $image */
            $image = $FeedbackImageRepository->findOneBy(['feedback' => $uploadFeedback]);

            if ($image) {
                $image->setFeedback($object);
                $object->addImages($image);
                $feedbackManager->persist($object);
            }

            $feedbackManager->flush();
            $feedbackManager->remove($uploadFeedback);
            $feedbackManager->flush();
        }
    }
}