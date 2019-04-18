<?php

namespace AdminBundle\Admin;

use AppBundle\Entity\Product;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CategoryAdmin
 * @package AdminBundle\Admin
 */
final class CategoryAdmin extends AbstractAdmin
{
    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id', IntegerType::class)
            ->add('name', TextType::class)
            ->add('active', BooleanType::class)
            ->add('products', CollectionType::class, [
                'type_options' => [
                    'delete'         => false,
                    'delete_options' => [
                        'type'         => HiddenType::class,
                        'type_options' => [
                            'mapped'   => false,
                            'required' => false,
                        ],
                    ],
                ],
            ], [
                'edit'     => 'inline',
                'inline'   => 'table',
                'sortable' => 'position',
            ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
    }
}