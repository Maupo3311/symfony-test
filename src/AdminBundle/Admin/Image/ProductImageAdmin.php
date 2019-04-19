<?php

namespace AdminBundle\Admin\Image;

use AppBundle\Entity\Image\ProductImage;
use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelHiddenType;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class ProductImageAdmin
 * @package AdminBundle\Admin\Image
 */
class ProductImageAdmin extends AbstractAdmin
{
    /**
     * @return ProductImage|mixed
     */
    public function getNewInstance()
    {
        /** @var ProductImage $instance */
        $instance = parent::getNewInstance();

        if (!$product = $instance->getProduct()) {
            $refererUrl = $this->getRequest()->headers->get('referer');
            $productId = basename(str_replace('/edit', '', $refererUrl));

            if ($productId) {
                /** @var ModelManager $modelManager */
                $modelManager = $this->modelManager;

                /** @var ProductRepository $product Repository */
                $productRepository = $modelManager
                    ->getEntityManager(Product::class)
                    ->getRepository(Product::class);

                $product = $productRepository->find($productId);

            }
        }

        $instance->setProduct($product);

        return $instance;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var ProductImage $subject */
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
            ->add('product', ModelHiddenType::class)
            ->add('file', FileType::class, ['label' => false], $fileFieldOptions);
    }

    /**
     * @param ProductImage $object
     * @return string
     */
    public function toString($object)
    {
        return $object instanceof ProductImage
            ? $object->getProductId()
            : 'ProductImage';
    }
}