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
     * @Route("/", name="product_show")
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function indexAction(Request $request)
    {
        $page = ($request->get('page')) ? $request->get('page') : 1;

        $theNumberOnThePage = 10;

        /** @var ProductRepository $productsRepository */
        $productsRepository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        $products              = $productsRepository->findByPage($page, $theNumberOnThePage);
        $quantityOfAllProducts = $productsRepository->getTheQuantityOfAllProducts();
        $numberOfPages         = ceil($quantityOfAllProducts / $theNumberOnThePage);

        $service  = $this->container->get('app.pagination');
        $position = $service->getHrefPosition($page, $numberOfPages);

        return $this->render('product/index.html.twig', compact('products', 'page', 'theNumberOnThePage', 'quantityOfAllProducts', 'position', 'numberOfPages'));
    }

    /**
     * @Route("/show/{id}", name="product_item", requirements={"id": "[0-9]+"})
     * @param Product $product
     * @return Response
     */
    public function showAction(Product $product)
    {
        return $this->render('product/show.html.twig', compact('product'));
    }

    /**
     * @Route("/category/{id}", name="show_by_category")
     * @param Category $category
     * @param Request  $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function showByCategory(Category $category, Request $request)
    {
        $page = ($request->get('page')) ? $request->get('page') : 1;

        $theNumberOnThePage = 10;

        /** @var ProductRepository $productRepository */
        $productRepository = $this
            ->getDoctrine()
            ->getRepository(Product::class);

        $products = $productRepository->findByCategory($category);
        $quantityOfAllProducts = $productRepository->getTheQuantityOfAllProducts();
        $numberOfPages         = ceil($quantityOfAllProducts / $theNumberOnThePage);

        $service = $this->container->get('app.pagination');
        $position  = $service->getHrefPosition($page, $numberOfPages);

        return $this->render('product/index.html.twig', [
            'page' => $page,
            'theNumberOnThePage' => $theNumberOnThePage,
            'products' => $products,
            'numberOfPages' => $numberOfPages,
            'position' => $position,
            'quantityOfAllProducts' => $quantityOfAllProducts,
        ]);
    }
}