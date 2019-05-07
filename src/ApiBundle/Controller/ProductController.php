<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Repository\CategoryRepository;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Hoa\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Swagger\Annotations as SWG;

/**
 * Class ProductController
 * @Route("/api")
 * @package ApiBundle\Controller
 */
class ProductController extends BaseController
{
    /**
     * @Rest\Get("/product")
     * @SWG\Response(
     *     response=200,
     *     description="For stanadrt will return 10 products on 1 page,
     *          is governed by the parameters limit and page",
     *     @Model(type=Product::class)
     * )
     * @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     type="integer",
     *     description="Pagination page"
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     type="integer",
     *     description="Number of products per page"
     * )
     * @param Request $request
     * @return View|mixed
     */
    public function getAllAction(Request $request)
    {
        try {
            $page = $request->get('page') ?: 1;
            $limit = $request->get('limit') ?: 10;

            /** @var ProductRepository $productRepository */
            $productRepository = $this->getDoctrine()->getRepository(Product::class);

            $restresult = $productRepository->findByPage($page, $limit);

            if ($restresult === null) {
                return $this->errorResponse("products not found", 404);
            }
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode());
        }

        return $restresult;
    }

    /**
     * @Rest\get("/product/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the product with the specified id",
     *     @Model(type=Product::class)
     * )
     * @param int $id
     * @return View|object[]
     */
    public function getAction(int $id)
    {
        $restresult = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($restresult === null) {
            return $this->errorResponse("there are no users exist", 404);
        }

        return $restresult;
    }

    /**
     * @Rest\Post("/product")
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        /** @var Category $category */
        if (!$category = $categoryRepository->find($request->get('category_id'))) {
            return $this->errorResponse('Category Not Found', 404);
        };

        $product = new Product();
        $product
            ->setTitle($request->get('title'))
            ->setRating($request->get('rating'))
            ->setNumber($request->get('number'))
            ->setDescription($request->get('description'))
            ->setCategory($category)
            ->setPrice($request->get('price'));

        $em->persist($product);
        $em->flush();

        return $this->successResponse('Product Added Successfully');
    }

    /**
     * @Rest\Put("/product/{id}")
     * @param int     $id
     * @param Request $request
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function putAction(int $id, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var ProductRepository $productRepository */
        $productRepository = $this->getDoctrine()->getRepository(Product::class);

        /** @var Product $product */
        if (!$product = $productRepository->find($id)) {
            return $this->errorResponse('Product Not Found', 404);
        }

        $changed = [];

        if ($request->get('title')){
            $product->setTitle($request->get('title'));
            $changed[] = 'title';
        }
        if ($request->get('description')){
            $product->setDescription($request->get('description'));
            $changed[] = 'description';
        }
        if ($request->get('price')){
            $product->setPrice($request->get('price'));
            $changed[] = 'price';
        }
        if ($request->get('rating')){
            $product->setRating($request->get('rating'));
            $changed[] = 'rating';
        }
        if ($request->get('number')){
            $product->setNumber($request->get('number'));
            $changed[] = 'number';
        }

        $em->persist($product);
        $em->flush();

        if (empty($changed)) {
            return $this->successResponse('Product has not been changed');
        } else {
            $message = 'Has been changed in the product: ';

            foreach ($changed as $item){
                $message .= "{$item}, ";
            }

            return $this->successResponse($message);
        }
    }

    /**
     * @Rest\Delete("/product/{id}")
     * @param $id
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAction($id){
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var ProductRepository $productRepository */
        $productRepository = $this->getDoctrine()->getRepository(Product::class);

        if(!$product = $productRepository->find($id)){
            return $this->errorResponse('Product Not Found', 404);
        }

        $em->remove($product);
        $em->flush();

        return $this->successResponse('Product successfully removed');
}
}