<?php

namespace AdminBundle\Admin;

use AdminBundle\Entity\Image;
use AppBundle\Entity\Feedback;
use AppBundle\Entity\Image\FeedbackImage;
use AppBundle\Entity\User;
use AppBundle\Enum\ImageType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class FeedbackAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
//            ->add('id')
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
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name');
    }

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
        /** @var Feedback $object */

        if ($object->getImages()) {
            /** @var FeedbackImage $attachedFile */
            foreach ($object->getImages() as $attachedFile) {
                $attachedFile->setCreatedAt(new \DateTime());
            }
        }
    }
}