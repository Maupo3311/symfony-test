<?php

namespace AppBundle\Services;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Shop;
use AppBundle\Repository\ProductRepository;
use AppBundle\Repository\ShopRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SortingService
 * @package AppBundle\Services
 */
class SortingService
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var CoordinateService
     */
    protected $coordinateService;

    /**
     * SortingService constructor.
     * @param EntityManager          $entityManager
     * @param Request                $request
     * @param CoordinateService|null $coordinateService
     */
    public function __construct(EntityManager $entityManager, Request $request, CoordinateService $coordinateService = null)
    {
        $this->entityManager     = $entityManager;
        $this->request           = $request;
        $this->coordinateService = $coordinateService;

        $this->parameters['page']               = trim($this->request->get('page') ?: 1);
        $this->parameters['order']              = trim($this->request->get('order') ?: 'ASC');
        $this->parameters['field']              = trim($this->request->get('sort') ?: 'id');
        $this->parameters['search']             = trim($this->request->get('search') ?: '');
        $this->parameters['filtrationField']    = trim(mb_strtolower($this->request->get('filtrationField') ?: 'rating'));
        $this->parameters['from']               = trim($this->request->get('from') ?: 0);
        $this->parameters['to']                 = trim($this->request->get('to') ?: 99999);
        $this->parameters['range']              = trim($this->request->get('range') ?: 0);
        $this->parameters['theNumberOnThePage'] = 15;

        if ($this->parameters['from'] > $this->parameters['to']) {
            $temporaryVariable        = $this->parameters['from'];
            $this->parameters['from'] = $this->parameters['to'];
            $this->parameters['to']   = $temporaryVariable;
        }
    }

    /**
     * @param $theNumberOnThePage
     * @return $this
     */
    public function setTheNumberOnThePage($theNumberOnThePage)
    {
        $this->parameters['theNumberOnThePage'] = $theNumberOnThePage;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager
            ->getRepository(Product::class);

        if ($this->parameters['range'] != 0) {
            /** @var ShopRepository $shopRepository */
            $shopRepository = $this->entityManager
                ->getRepository(Shop::class);

            $allShops = $shopRepository->findAll();

            return $productRepository->findByParameters(
                $this->parameters,
                $this->coordinateService->GetSuitableShopCategories($allShops, $this->parameters['range']));
        } else {
            return $productRepository->findByParameters($this->parameters);
        }
    }

    /**
     * @param Category $category
     * @return mixed
     */
    public function getProductsByCategory(Category $category)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager
            ->getRepository(Product::class);

        return $productRepository->findByParametersAndCategory($this->parameters, $category);
    }

    /**
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getQuantityProducts()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager
            ->getRepository(Product::class);

        if ($this->parameters['range'] != 0) {
            /** @var ShopRepository $shopRepository */
            $shopRepository = $this->entityManager
                ->getRepository(Shop::class);

            $allShops = $shopRepository->findAll();

            return $productRepository->getQuantityByParameters(
                $this->parameters,
                $this->coordinateService->GetSuitableShopCategories($allShops, $this->parameters['range']));
        } else {
            return $productRepository->getQuantityByParameters($this->parameters);
        }
    }

    /**
     * @param Category $category
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getQuantityProductsByCategory(Category $category)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager
            ->getRepository(Product::class);

        return $productRepository->getQuantityByParametersAndCategory($this->parameters, $category);
    }

    /**
     * @param bool $withSorting
     * @return PaginationService
     * @throws NonUniqueResultException
     */
    public function getProductPagination($withSorting = false)
    {
        /** @var PaginationService $pagination */
        $pagination = new PaginationService(
            $this->parameters['page'],
            $this->getQuantityProducts(),
            $this->parameters['theNumberOnThePage']
        );

        if ($withSorting) {
            $pagination->setSort($this->parameters['order'], $this->parameters['field']);
        }

        return $pagination;
    }

    /**
     * @param Category $category
     * @param bool     $withSorting
     * @return PaginationService
     * @throws NonUniqueResultException
     */
    public function getProductPaginationByCategory(Category $category, $withSorting = false)
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager
            ->getRepository(Product::class);

        /** @var PaginationService $pagination */
        $pagination = new PaginationService(
            $this->parameters['page'],
            $productRepository->getQuantityByParametersAndCategory($this->parameters, $category),
            $this->parameters['theNumberOnThePage']
        );

        if ($withSorting) {
            $pagination->setSort($this->parameters['order'], $this->parameters['field']);
        }

        return $pagination;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}