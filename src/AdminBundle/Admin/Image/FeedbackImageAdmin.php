<?php

namespace AdminBundle\Admin\Image;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\Image\FeedbackImage;
use AppBundle\Repository\FeedbackRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelHiddenType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class DealAttachedFileAdmin
 * @package AdminBundle\Admin\Image
 */
class FeedbackImageAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        /** @var FeedbackImage $instance */
        $instance = parent::getNewInstance();

        if (!$feedback = $instance->getFeedback()) {
            $refererUrl = $this->getRequest()->headers->get('referer');
            $feedbackId = basename(str_replace('/edit', '', $refererUrl));

            if ($feedbackId) {
                /** @var FeedbackRepository $feedbackRepository */
                $feedbackRepository = $this->modelManager
                    ->getEntityManager(Feedback::class)
                    ->getRepository(Feedback::class);

                $feedback = $feedbackRepository->find($feedbackId);
            }
        }

        $instance->setFeedback($feedback);

        return $instance;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var FeedbackImage $subject */
        $subject = $this->getSubject();

        $fileFieldOptions = ['required' => false];

        if ($subject && $webPath = $subject->getImageWebPath()) {
//            $container = $this->getConfigurationPool()->getContainer();
//            $basePath  = $container->get('request_stack')->getCurrentRequest()->getBasePath();
//            $fullPath  = $basePath . '/' . $webPath;
            $fullPath = '';

            $fileFieldOptions['attr'] = ['hidden' => true];

            $fileFieldOptions['help'] = '
            <a href="' . $fullPath . '" target="_blank">
                <img 
                    alt="' . $subject->getFilePath() . '" 
                    title="' . $subject->getFilePath() . '"
                    src="' . $fullPath . '" 
                    class="admin-preview" style="width:100%" 
                /> 
            </a>';
        }

        $formMapper
            ->add('feedback', ModelHiddenType::class)
            ->add('file', FileType::class, ['label' => false], $fileFieldOptions);
    }

    /**
     * @param mixed $object
     * @return string
     */
    public function toString($object)
    {
        return $object instanceof FeedbackImage
            ? $object->getName()
            : 'FeedbackImage';
    }
}