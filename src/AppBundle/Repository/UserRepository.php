<?php

namespace AppBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * Class UserRepository
 * @package AppBundle\Repository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param int $page
     * @param int $theNumberOnThePage
     * @return mixed
     */
    public function findByPage(int $page, int $theNumberOnThePage)
    {
        $lastResult  = $page * $theNumberOnThePage;
        $firstResult = $lastResult - $theNumberOnThePage;

        return $this
            ->createQueryBuilder('u')
            ->setFirstResult($firstResult)
            ->setMaxResults($theNumberOnThePage)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $username
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findByUsername($username)
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $apiKey
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findByApiKey($apiKey)
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.apiKey = :apiKey')
            ->setParameter('apiKey', $apiKey)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
