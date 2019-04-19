<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            'current_order'         => $order
        ]);
    }

    /**
     * Single product information
     *
     * @Route("/show/{id}", name="product_item", requirements={"id": "[0-9]+"})
     * @param Product $product
     * @return Response
     */
    public function showAction(Product $product)
    {
        return $this->render('product/show.html.twig', compact('product'));
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
            'current_order'         => $order
        ]);
    }
}