<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Services\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * List of categories.
     *
     * @Route("/", name="category_show")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request) :Response
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this
            ->getDoctrine()
            ->getRepository(Category::class);

        $theNumberOnThePage = 10;
        $page               = $request->get('page') ?: 1;
        $countCategories    = $categoryRepository->count([]);
        $numberOfPages      = ceil($countCategories / $theNumberOnThePage);
        $categories         = $categoryRepository->findByPage($page, $theNumberOnThePage);

        /** @var PaginationService $service */
        $service  = $this->container->get('app.pagination');
        $position = $service->getHrefPosition($page, $numberOfPages);

        return $this->render(
            'category/index.html.twig',
            compact('categories', 'page', 'theNumberOnThePage', 'countCategories', 'numberOfPages', 'position')
        );
    }
}
