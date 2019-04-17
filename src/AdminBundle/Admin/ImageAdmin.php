<?php

namespace AdminBundle\Admin;

use AdminBundle\Entity\Image;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class ImageAdmin
 * @package AdminBundle\Admin
 */
final class ImageAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('file', FileType::class, [
                'required' => false
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('updated');
    }

    /**
     * @param $image
     */
    public function prePersist($image)
    {
        $this->manageFileUpload($image);
    }

    /**
     * @param Image $image
     */
    public function preUpdate($image)
    {
        $this->manageFileUpload($image);
    }

    /**
     * @param Image $image
     */
    private function manageFileUpload(Image $image)
    {
        if ($image->getFile()) {
            $image->refreshUpdated();
        }
    }
}