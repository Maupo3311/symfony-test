<?php

namespace AppBundle\Controller;

use EntityBundle\Entity\Feedback;
use EntityBundle\Entity\Image\FeedbackImage;
use EntityBundle\Entity\Product;
use EntityBundle\Entity\Shop;
use EntityBundle\Entity\User;
use AppBundle\Form\FeedbackType;
use EntityBundle\Repository\FeedbackImageRepository;
use EntityBundle\Repository\ProductRepository;
use EntityBundle\Repository\ShopRepository;
use AppBundle\Services\FileUploader;
use AppBundle\Services\IpstackService;
use AppBundle\Services\PaginationService;
use OK\Ipstack\Exceptions\InvalidApiException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EntityBundle\Repository\FeedbackRepository;

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
     * @throws InvalidApiException
     */
    public function indexAction()
    {
        $testIp = [
            '94.251.80.158', // Новосибирск
            '195.208.159.255', // Ставрополь
            '178.219.186.12', // Москва
        ];

        /** @var IpstackService $ipstack */
        $ipstack = $this->get('app.ipstack');

        $ipstack->addInSession($testIp[0]);

        /**@var ProductRepository $productRepository */
        $productRepository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        /** @var ShopRepository $shopRepository */
        $shopRepository = $this->getDoctrine()->getRepository(Shop::class);

        $bestProducts = $productRepository->findBestProducts();
        $shops        = $shopRepository->findAll();

        return $this->render('main/index.html.twig', [
            'best_products' => $bestProducts,
            'shops'         => $shops,
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

        /** @var PaginationService $pagination */
        $pagination = new PaginationService(
            ($request->get('page')) ? $request->get('page') : 1,
            $feedbackRepository->getTheQuantityOfAllFeedbacks(),
            5

        );

        $allFeedbacks = $feedbackRepository->findByPage($pagination->getPage(), $pagination->getTheNumberOnThePage());
        $pathForFiles = $fileUploader->getTargetDirectory();

        $form = $this->createForm(FeedbackType::class);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        $formView = $form->createView();

        return $this->render('main/feedback.html.twig', [
            'formView'       => $formView,
            'allFeedbacks'   => $allFeedbacks,
            'pagination'     => $pagination,
            'path_for_files' => $pathForFiles,
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
