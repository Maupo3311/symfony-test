<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\Image\FeedbackImage;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\FeedbackType;
use AppBundle\Repository\FeedbackImageRepository;
use AppBundle\Repository\ProductRepository;
use AppBundle\Services\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Repository\FeedbackRepository;

/**
 * Class MainController
 * @package AppBundle\Controller
 */
class MainController extends Controller
{
    /**
     * Shows the home page of the site
     *
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        /**@var ProductRepository $productRepository */
        $productRepository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        $bestProducts = $productRepository->findBestProducts();

        return $this->render('main/index.html.twig', [
            'best_products' => $bestProducts,
        ]);
    }

    /**
     * Shows the feedbacks of our site and allows you to leave your
     *
     * @Route("/feedback", name="feedback")
     * @param Request      $request
     * @param FileUploader $fileUploader
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function feedbackAction(Request $request, FileUploader $fileUploader)
    {
        /** @var FeedbackRepository $feedbackRepository */
        $feedbackRepository = $this
            ->getDoctrine()
            ->getRepository(Feedback::class);

        $page                   = ($request->get('page')) ? $request->get('page') : 1;
        $theNumberOnThePage     = 5;
        $allFeedbacks           = $feedbackRepository->findByPage($page, $theNumberOnThePage);
        $quantityOfAllFeedbacks = $feedbackRepository->getTheQuantityOfAllFeedbacks();
        $numberOfPages          = ceil($quantityOfAllFeedbacks / $theNumberOnThePage);
        $pathForFiles           = $fileUploader->getTargetDirectory();

        $service  = $this->container->get('app.pagination');
        $position = $service->getHrefPosition($page, $numberOfPages);

        $form = $this->createForm(FeedbackType::class);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        /**@var User $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($user)) {
                /** @var Feedback $feedback */
                $feedback = $form->getData();
                /** @var EntityManager $em */
                $em = $this->getDoctrine()->getManager();

                $images = $feedback->getImages();

                $feedbackImages = [];
                if ($images) {
                    foreach ($images as $image) {
                        /** @var FeedbackImage $feedbackImage */
                        $feedbackImage = new FeedbackImage();
                        $feedbackImage
                            ->setFile($image)
                            ->uploadImage()
                            ->setFeedback($feedback);
                        $feedbackImages[] = $feedbackImage;
                        $em->persist($feedbackImage);
                    }
                }

                $feedback
                    ->setImages($feedbackImages)
                    ->setUser($user)
                    ->setName($user->getFirstName() . ' ' . $user->getLastName())
                    ->setEmail($user->getEmail());

                $em->persist($feedback);
                $em->flush();

                $this->addFlash('success', 'Saved!');
            } else {
                $this->addFlash('error', 'You can\'t send feedback to unauthorized people!');
            }

            return $this->redirectToRoute('feedback');
        }

        $formView = $form->createView();

        return $this->render('main/feedback.html.twig', [
            'formView'               => $formView,
            'allFeedbacks'           => $allFeedbacks,
            'page'                   => $page,
            'theNumberOnThePage'     => $theNumberOnThePage,
            'quantityOfAllFeedbacks' => $quantityOfAllFeedbacks,
            'numberOfPages'          => $numberOfPages,
            'position'               => $position,
            'path_for_files'         => $pathForFiles,
        ]);
    }

    /**
     * Remove feedback and all related files
     *
     * @Route("feedback/delete/{id}", name="delete_feedback")
     * @param Feedback $feedback
     * @return RedirectResponse
     */
    public function deleteFeedbackAction(Feedback $feedback)
    {
        /**@var User $user */
        $user = $this->getUser();

        /** @var FeedbackImageRepository $feedbackImageRepository */
        $em = $this->getDoctrine()->getManager();

        if ($feedback->getUser()->getId() == $user->getId()) {

            $images = $feedback->getImages()->getValues();
            /** @var FeedbackImage $image */
            foreach ($images as $image) {
                $image
                    ->storeImageFilenameForRemove()
                    ->removeImageUpload();
            }

            $em->remove($feedback);
            $em->flush();

            $this->addFlash('success', 'Your review was successfully removed!');

            return $this->redirectToRoute('feedback');
        } else {
            $this->addFlash('error', 'You cannot delete this review!');

            return $this->redirectToRoute('feedback');
        }
    }
}
