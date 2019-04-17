<?php


namespace AdminBundle\Admin;

use AppBundle\Entity\Category;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\Form\Type\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class ProductAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('category', EntityType::class, [
                'class' => 'AppBundle\Entity\Category',
                'choice_label' => 'name',
            ])
            ->add('title')
            ->add('price')
            ->add('description', TextareaType::class)
            ->add('active', BooleanType::class)
            ->add('rating');
//            ->add('category', ModelType::class, [
//            'class' => Category::class,
//            'property' => 'name',
//            ]);
    }
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title');
    }
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->addIdentifier('price');
    }
}