<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Hoa\Exception\Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use phpDocumentor\Reflection\DocBlock\Tags\Deprecated;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use FOS\RestBundle\View\View;

/**
 * Class CategoryController
 * @Route("/api")
 * @package ApiBundle\Controller
 */
class CategoryController extends BaseController
{
    /**
     * @Rest\Get("/category")
     * @SWG\Response(
     *     response=200,
     *     description="For stanadrt will return 10 categories on 1 page,
     *          is governed by the parameters limit and page",
     *     @Model(type=Category::class)
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
     *     description="Number of categories per page"
     * )
     * @SWG\Tag(name="category")
     * @param Request $request
     * @return View|mixed
     */
    public function getAllAction(Request $request)
    {
        try {
            $page  = $request->get('page') ?: 1;
            $limit = $request->get('limit') ?: 10;

            /** @var CategoryRepository $categoryRepository */
            $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

            $restresult = $categoryRepository->findByPage($page, $limit);

            if ($restresult === null) {
                return $this->errorResponse("category not found", 404);
            }
        } catch (Exception $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->getCode());
        }

        return $restresult;
    }

    /**
     * @Rest\get("/category/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the category with the specified id",
     *     @Model(type=Category::class)
     * )
     * @SWG\Tag(name="category")
     * @param int $id
     * @return View|object[]
     */
    public function getAction(int $id)
    {
        $restresult = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($restresult === null) {
            return $this->errorResponse("there are no category exist", 404);
        }

        return $restresult;
    }

    /**
     * @Rest\Post("/category")
     * @SWG\Response(
     *     response=200,
     *     description="Object with success message",
     *     @Model(type=Category::class)
     * )
     * @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     type="string",
     *     description="Name category"
     * )
     * @SWG\Tag(name="category")
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

        $category = new Category();
        $category->setName($request->get('name'));

        $em->persist($category);
        $em->flush();

        return $this->successResponse('Category Added Successfully');
    }

    /**
     * @Rest\Put("/category/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Object with a message what fields have been changed",
     *     @Model(type=Category::class)
     * )
     * @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     type="string",
     *     description="Name category"
     * )
     * @SWG\Tag(name="category")
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

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        /** @var Category $category */
        if (!$category = $categoryRepository->find($id)) {
            return $this->errorResponse('Category Not Found', 404);
        }

        $category->setName($request->get('name'));

        $em->persist($category);
        $em->flush();

        return $this->successResponse('Category successfully changed');
    }

    /**
     * @Rest\Delete("/category/{id}")
     * @SWG\Response(
     *     response=200,
     *     description="Object with success message",
     *     @Model(type=Category::class)
     * )
     * @SWG\Tag(name="category")
     * @param $id
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        if (!$category = $categoryRepository->find($id)) {
            return $this->errorResponse('Category Not Found', 404);
        }

        $em->remove($category);
        $em->flush();

        return $this->successResponse('Category successfully removed');
    }

    /**
     * @Rest\Get("/category/{id}/products")
     * @SWG\Response(
     *     response=200,
     *     description="An array of products in this category",
     *     @Model(type=Category::class)
     * )
     * @SWG\Tag(name="category")
     * @param int $id
     * @return ArrayCollection|Response
     */
    public function getProductsByCategory(int $id)
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        /** @var Category $category */
        if (!$category = $categoryRepository->find($id)) {
            return $this->errorResponse('Category not found', 404);
        }

        return $category->getProducts();
    }
}