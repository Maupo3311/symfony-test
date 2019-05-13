<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\Image\FeedbackImage;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class ProductAdmin
 * @package AdminBundle\Admin
 * @ORM\HasLifecycleCallbacks()
 */
final class CommentAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('user', EntityType::class, [
                'class'        => User::class,
                'choice_label' => 'username',
            ])
            ->add('product', EntityType::class, [
                'class'        => Product::class,
                'choice_label' => 'title',
            ])
            ->add('message', TextareaType::class);

        $requestUri = $this->getRequest()->getRequestUri();
        $commentId  = basename(str_replace('/edit', '', $requestUri));

        if ($this->getRoot()->getClass() === 'AppBundle\Entity\Comment' && $commentId != 'create') {
            $formMapper->add('images', CollectionType::class, [
                'required' => false,
            ], [
                'edit'     => 'inline',
                'sortable' => 'position',
            ]);
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('message');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('message')
            ->add('user.username');
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
}