<?php

namespace AppBundle\Services;

use EntityBundle\Entity\Category;
use EntityBundle\Entity\Product;
use EntityBundle\Entity\Shop;
use EntityBundle\Repository\ProductRepository;
use EntityBundle\Repository\ShopRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * @var Session
     */
    protected $session;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * SortingService constructor.
     * @param Session       $session
     * @param EntityManager $entityManager
     */
    public function __construct(Session $session, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->session       = $session;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        $this->parameters['page']               = trim($this->request->request->get('page') ?: 1);
        $this->parameters['order']              = trim($this->request->request->get('order') ?: 'ASC');
        $this->parameters['field']              = trim($this->request->request->get('sort') ?: 'id');
        $this->parameters['search']             = trim($this->request->request->get('search') ?: '');
        $this->parameters['priceFrom']          = trim($this->request->request->get('priceFrom'));
        $this->parameters['priceTo']            = trim($this->request->request->get('priceTo'));
        $this->parameters['ratingFrom']         = trim($this->request->request->get('ratingFrom'));
        $this->parameters['ratingTo']           = trim($this->request->request->get('ratingTo'));
        $this->parameters['range']              = trim($this->request->request->get('range'));
        $this->parameters['theNumberOnThePage'] = 15;

        if ($this->parameters['priceFrom'] > $this->parameters['priceTo']) {
            $temporaryVariable             = $this->parameters['priceFrom'];
            $this->parameters['priceFrom'] = $this->parameters['priceTo'];
            $this->parameters['priceTo']   = $temporaryVariable;
        }

        if ($this->parameters['ratingFrom'] > $this->parameters['ratingTo']) {
            $temporaryVariable              = $this->parameters['ratingFrom'];
            $this->parameters['ratingFrom'] = $this->parameters['ratingTo'];
            $this->parameters['ratingTo']   = $temporaryVariable;
        }

        return $this;
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
    public function getProductsByRequest()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager
            ->getRepository(Product::class);

        if (!empty($this->parameters['range'])) {

            /** @var ShopRepository $shopRepository */
            $shopRepository = $this->entityManager
                ->getRepository(Shop::class);

            $userLocation = $this->session->get('userLocation');

            $availableShops = $shopRepository->findToASpecifiedRadius(
                $userLocation->getLatitude(),
                $userLocation->getLongitude(),
                $this->parameters['range']
            );

            return $productRepository->findByParameters($this->parameters, $availableShops);
        } else {
            return $productRepository->findByParameters($this->parameters);
        }
    }

    /**
     * @param Category $category
     * @return mixed
     */
    public function getProductsByRequestAndCategory(Category $category)
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
    public function getQuantityProductsByRequest()
    {
        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager
            ->getRepository(Product::class);

        if (!empty($this->parameters['range'])) {
            /** @var ShopRepository $shopRepository */
            $shopRepository = $this->entityManager
                ->getRepository(Shop::class);

            $userLocation = $this->session->get('userLocation');

            $availableShops = $shopRepository->findToASpecifiedRadius(
                $userLocation->getLatitude(),
                $userLocation->getLongitude(),
                $this->parameters['range']
            );

            return $productRepository->getQuantityByParameters($this->parameters, $availableShops);
        } else {
            return $productRepository->getQuantityByParameters($this->parameters);
        }
    }

    /**
     * @param Category $category
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getQuantityProductsByRequestAndCategory(Category $category)
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
            $this->getQuantityProductsByRequest(),
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
        /** @var PaginationService $pagination */
        $pagination = new PaginationService(
            $this->parameters['page'],
            $this->getQuantityProductsByRequestAndCategory($category),
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