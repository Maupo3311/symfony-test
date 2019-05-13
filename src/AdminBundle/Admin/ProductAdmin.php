<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\Image\FeedbackImage;
use Doctrine\DBAL\Types\DecimalType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class ProductAdmin
 * @package AdminBundle\Admin
 * @ORM\HasLifecycleCallbacks()
 */
final class ProductAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('category', EntityType::class, [
                'class'        => 'AppBundle\Entity\Category',
                'choice_label' => 'name',
            ])
            ->add('title')
            ->add('price')
            ->add('description', TextareaType::class)
            ->add('active', BooleanType::class)
            ->add('rating')
            ->add('number');

        $requestUri = $this->getRequest()->getRequestUri();
        $productId  = basename(str_replace('/edit', '', $requestUri));

        if ($this->getRoot()->getClass() === 'AppBundle\Entity\Product' && $productId != 'create') {
            $formMapper->
            add('images', CollectionType::class, [
                'required' => false,
            ], [
                'edit'     => 'inline',
                'sortable' => 'position',
            ])
                ->add('comments', CollectionType::class, [
                    'required' => false,
                ], [
                    'edit'     => 'inline',
                    'inline'   => 'table',
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
            ->add('title');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('price', DecimalType::class)
            ->add('description')
            ->add('category.name');
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