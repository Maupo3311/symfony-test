<?php

namespace AppBundle\Controller;

use EntityBundle\Entity\Basket;
use EntityBundle\Entity\Category;
use EntityBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Services\SortingService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EntityBundle\Entity\Product;

/**
 * Class ProductController
 * @package AppBundle\Controller
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * All products sheet
     *
     * @Route("/", name="product_show")
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function indexAction(Request $request)
    {
        /** @var SortingService $sortingService */
        $sortingService = $this->get('app.sorting');

        $sortingService->setRequest($request);
        $pagination = $sortingService->getProductPagination(true);
        $products   = $sortingService->getProductsByRequest();

        return $this->render('product/index.html.twig', [
            'products'        => $products,
            'pagination'      => $pagination,
            'sorting_service' => $sortingService,
        ]);
    }

    /**
     * Single product information
     *
     * @Route("/show/{id}", name="product_item", requirements={"id": "[0-9]+"})
     * @param Product $product
     * @param Request $request
     * @return Response
     */
    public function showAction(Product $product, Request $request)
    {
        $form = $this->createForm(CommentType::class);
        $form->add('submit', SubmitType::class, ['attr' => ['class' => 'btn btn-info pull-right']]);
        $form->handleRequest($request);

        return $this->render('product/show.html.twig', [
            'product'      => $product,
            'comment_form' => $form->createView(),
        ]);
    }

    /**
     * Show products by category
     *
     * @Route("/category/{id}", name="show_by_category")
     * @param Category $category
     * @param Request  $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showByCategoryAction(Category $category, Request $request)
    {
        /** @var SortingService $sortingService */
        $sortingService = $this->get('app.sorting');

        $sortingService->setRequest($request);
        $pagination = $sortingService->getProductPaginationByCategory($category, true);
        $products   = $sortingService->getProductsByRequestAndCategory($category);

        return $this->render('product/index.html.twig', [
            'products'        => $products,
            'pagination'      => $pagination,
            'sorting_service' => $sortingService,
            'by_category'     => $category,
        ]);
    }

    /**
     * Adds the product to the user's basket
     *
     * @Route("/add-to-basket/{id}", name="add_product_to_basket")
     * @param Product $product
     * @param Request $request
     * @return RedirectResponse
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addToBasketAction(Product $product, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (empty($user)) {
            $this->addFlash('error', 'You are not authorized');

            return $this->redirectToRoute('product_show');
        }

        $number = ($request->request->get('number')) ?: 1;

        $existingBasketItem = null;

        if ($product->getNumber() < $number) {
            $this->addFlash('error', 'There are so many product in stock');

            return $this->redirectToRoute('product_show');
        }

        /** @var Basket $basketItem */
        foreach ($user->getBasketItems() as $basketItem) {
            $basketProduct = $basketItem->getBasketProduct();
            if ($basketProduct === $product) {
                if ($basketProduct->getNumber() - $basketItem->getNumberOfProducts() - $number < 0) {
                    $this->addFlash('error', 'There are so many product in stock');

                    return $this->redirectToRoute('product_show');
                } else {
                    /** @var Basket $existingBasketItem */
                    $existingBasketItem = $basketItem;
                    break;
                }
            }
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if ($existingBasketItem) {
            $existingBasketItem
                ->setNumberOfProducts($existingBasketItem->getNumberOfProducts() + $number);

            $em->persist($existingBasketItem);
        } else {
            $basketItem = new Basket();

            $basketItem->setBasketProduct($product)
                ->setUser($user)
                ->setNumberOfProducts($number);

            $em->persist($basketItem);
        }

        $em->flush();

        $this->addFlash('addProductSuccess', 'Product added to basket');

        return $this->redirectToRoute('product_show');
    }
}