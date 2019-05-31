<?php

namespace AdminBundle\Admin\Image;

use EntityBundle\Entity\Image\ShopImage;
use EntityBundle\Entity\Shop;
use AppBundle\Repository\ShopRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelHiddenType;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class ShopImageAdmin
 * @package AdminBundle\Admin\Image
 */
class ShopImageAdmin extends AbstractAdmin
{
    /**
     * @return ShopImage|mixed
     */
    public function getNewInstance()
    {
        /** @var ShopImage $instance */
        $instance = parent::getNewInstance();

        if (!$shop = $instance->getShop()) {
            $refererUrl = $this->getRequest()->headers->get('referer');
            $shopId  = basename(str_replace('/edit', '', $refererUrl));

            if ($shopId) {
                /** @var ModelManager $modelManager */
                $modelManager = $this->modelManager;

                /** @var ShopRepository $shopRepository */
                $shopRepository = $modelManager
                    ->getEntityManager(Shop::class)
                    ->getRepository(Shop::class);

                /** @var Shop $shop */
                $shop = $shopRepository->find($shopId);

            }
        }

        $instance->setShop($shop);

        return $instance;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var ShopImage $subject */
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
            ->add('shop', ModelHiddenType::class)
            ->add('file', FileType::class, ['label' => false], $fileFieldOptions);
    }

    /**
     * @param ShopImage $object
     * @return string
     */
    public function toString($object)
    {
        return 'shopImage';
    }
}