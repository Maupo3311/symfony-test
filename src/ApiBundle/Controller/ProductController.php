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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;

/**
 * Class ProductController
 * @Route("/api")
 * @package ApiBundle\Controller
 */
class ProductController extends BaseController
{
    /**
     * @Rest\Get("/product")
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
                return $this->errorResponse("products not found", Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode());
        }

        return $restresult;
    }

    /**
     * @Rest\get("/product/{id}")
     * @param int $id
     * @return View|object[]
     */
    public function getAction(int $id)
    {
        $restresult = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($restresult === null) {
            return $this->errorResponse("there are no users exist", Response::HTTP_NOT_FOUND);
        }

        return $restresult;
    }

    /**
     * @Rest\Post("/product")
     * @param Request $request
     * @return View
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
            return new View("Category Not Found", Response::HTTP_NOT_FOUND);
        };

        $product = new Product();
        $product
            ->setTitle($request->get('title'))
            ->setRating($data['rating'])
            ->setNumber($data['number'])
            ->setDescription($data['description'])
            ->setCategory($category)
            ->setPrice($data['price']);

        $em->persist($product);
        $em->flush();

        return new View("Product Added Successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/product/{id}")
     * @param int     $id
     * @param Request $request
     * @return View
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
            return new View("Product Not Found", Response::HTTP_NOT_FOUND);
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
            return new View("Product has not been changed", Response::HTTP_OK);
        } else {
            $string = 'Has been changed in the product: ';

            foreach ($changed as $item){
                $string .= "{$item}, ";
            }

            return new View($string, Response::HTTP_OK);
        }
    }

    /**
     * @Rest\Delete("/product/{id}")
     * @param $id
     * @return View
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
            return new View("Product Not Found", Response::HTTP_NOT_FOUND);
        }

        $em->remove($product);
        $em->flush();

        return new View("Product successfully removed", Response::HTTP_OK);
    }
}