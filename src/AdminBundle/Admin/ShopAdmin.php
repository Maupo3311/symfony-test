<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;

/**
 * Class ShopAdmin
 * @package AdminBundle\Admin
 * @ORM\HasLifecycleCallbacks()
 */
final class ShopAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name')
            ->add('description')
            ->add('phoneNumber');

        $requestUri = $this->getRequest()->getRequestUri();
        $shopId  = basename(str_replace('/edit', '', $requestUri));

        if ($this->getRoot()->getClass() === 'AppBundle\Entity\Shop' && $shopId != 'create') {
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
            ->add('name');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('description')
            ->add('phoneNumber');
    }
}