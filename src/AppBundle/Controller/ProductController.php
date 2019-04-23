<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Basket;
use AppBundle\Entity\Category;
use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Product;

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
        $page = ($request->get('page')) ? $request->get('page') : 1;

        /** @var ProductRepository $productsRepository */
        $productsRepository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        $theNumberOnThePage = 10;
        $field              = $request->get('sort') ?: 'id';
        $order              = $request->get('order') ? trim($request->get('order')) : 'ASC';
        $nextOrder          = $order == 'ASC' ? 'DESC' : 'ASC';
        $sort               = [$field => $order];
        $products           = $productsRepository->findBySortAndPage($sort, $page, $theNumberOnThePage);

        $quantityOfAllProducts = $productsRepository->getTheQuantityOfAllProducts();
        $numberOfPages         = ceil($quantityOfAllProducts / $theNumberOnThePage);

        $service  = $this->container->get('app.pagination');
        $position = $service->getHrefPosition($page, $numberOfPages);

        return $this->render('product/index.html.twig', [
            'products'              => $products,
            'page'                  => $page,
            'theNumberOnThePage'    => $theNumberOnThePage,
            'quantityOfAllProducts' => $quantityOfAllProducts,
            'position'              => $position,
            'numberOfPages'         => $numberOfPages,
            'nextOrder'             => $nextOrder,
            'sort'                  => $sort,
            'current_field'         => $field,
            'current_order'         => $order,
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
        $user = $this->getUser();

        $form = $this->createForm(CommentType::class);
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($user)) {
                /** @var Comment $comment */
                $comment = $form->getData();
                $em      = $this->getDoctrine()->getManager();

                $comment
                    ->setUser($user)
                    ->setProduct($product);

                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'You comment successful saved');

                return $this->redirectToRoute('product_item', ['id' => $product->getId()]);
            } else {
                $this->addFlash('success', 'You dont authorization');

                return $this->redirectToRoute('product_item', ['id' => $product->getId()]);
            }
        }

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
        $page = ($request->get('page')) ? $request->get('page') : 1;

        $theNumberOnThePage = 10;

        /** @var ProductRepository $productRepository */
        $productRepository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        $field                 = ($request->get('sort')) ? $request->get('sort') : 'id';
        $order                 = ($request->get('order')) ? trim($request->get('order')) : 'ASC';
        $nextOrder             = ($order == 'ASC') ? 'DESC' : 'ASC';
        $sort                  = [$field => $order];
        $products              = $productRepository->findByCategory($category, $page, $theNumberOnThePage, $sort);
        $quantityOfAllProducts = $productRepository->getTheQuantityOfAllProducts(['category' => $category->getId()]);
        $numberOfPages         = ceil($quantityOfAllProducts / $theNumberOnThePage);

        $service  = $this->container->get('app.pagination');
        $position = $service->getHrefPosition($page, $numberOfPages);

        return $this->render('product/index.html.twig', [
            'products'              => $products,
            'page'                  => $page,
            'theNumberOnThePage'    => $theNumberOnThePage,
            'quantityOfAllProducts' => $quantityOfAllProducts,
            'position'              => $position,
            'numberOfPages'         => $numberOfPages,
            'nextOrder'             => $nextOrder,
            'sort'                  => $sort,
            'current_field'         => $field,
            'current_order'         => $order,
        ]);
    }

    /**
     * @Route("/add-to-basket/{id}", name="add_product_to_basket")
     * @param Product $product
     * @return RedirectResponse
     */
    public function addToBasketAction(Product $product)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (empty($user)) {
            $this->addFlash('error', 'You are not authorized');

            return $this->redirectToRoute('product_show');
        }

        $basketItems = $product->getBasketItems();
        /** @var Basket $basketItem */
        foreach ($basketItems as $basketItem) {
            if ($basketItem->getUser() == $user) {
                $this->addFlash('error', 'This product is already in your basket');

                return $this->redirectToRoute('product_show');
            }
        }

        $em          = $this->getDoctrine()->getManager();
        $basket_item = new Basket();

        $basket_item
            ->setBasketProduct($product)
            ->setUser($user);

        $em->persist($basket_item);
        $em->flush();

        $this->addFlash('addProductSuccess', 'Product added to basket');

        return $this->redirectToRoute('product_show');
    }
}