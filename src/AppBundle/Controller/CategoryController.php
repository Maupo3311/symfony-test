<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/", name="category_show")
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function indexAction(Request $request)
    {
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this
            ->getDoctrine()
            ->getRepository(Category::class);

        $page = ($request->get('page')) ? $request->get('page') : 1;
        $theNumberOnThePage = 10;
        $quantityOfAllCategories = $categoryRepository->getTheQuantityOfAllCategories();
        $numberOfPages = ceil($quantityOfAllCategories / $theNumberOnThePage);
        $categories = $categoryRepository->findByPage($page, $theNumberOnThePage);

        $service = $this->container->get('app.pagination');
        $position = $service->getHrefPosition($page, $numberOfPages);

        return $this->render('category/index.html.twig', compact('categories', 'page', 'theNumberOnThePage', 'quantityOfAllCategories', 'numberOfPages', 'position'));
    }
}