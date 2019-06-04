<?php

namespace AppBundle\Controller;

use EntityBundle\Entity\Category;
use AppBundle\Services\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EntityBundle\Repository\CategoryRepository;
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
    public function indexAction(Request $request): Response
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this
            ->getDoctrine()
            ->getRepository(Category::class);

        /** @var PaginationService $pagination */
        $pagination = new PaginationService(
            $request->get('page') ?: 1,
            $categoryRepository->count([]),
            10
        );

        $categories = $categoryRepository->findByPage($pagination->getPage(), $pagination->getTheNumberOnThePage());

        return $this->render(
            'category/index.html.twig', [
                'categories' => $categories,
                'pagination' => $pagination,
            ]
        );
    }
}
