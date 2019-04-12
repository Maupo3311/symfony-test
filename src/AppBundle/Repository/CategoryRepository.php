<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getTheQuantityOfAllCategories(){
        return $this
            ->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $page
     * @param $theNumberOnThePage
     * @return mixed
     */
    public function findByPage($page, $theNumberOnThePage)
    {
        $lastResult = $page * $theNumberOnThePage;
        $firstResult = $lastResult - $theNumberOnThePage;

        return $this
            ->createQueryBuilder('c')
            ->setFirstResult($firstResult)
            ->setMaxResults($theNumberOnThePage)
            ->getQuery()
            ->getResult();
    }
}
