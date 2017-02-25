<?php

namespace AppBundle\Repository;

use AppBundle\Entity\News;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * NewsRepository
 */
class NewsRepository extends EntityRepository
{
    /**
     * @return Query
     */
    public function queryLatest()
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT n
                FROM AppBundle:News n
                WHERE n.created <= :now
                ORDER BY n.created DESC
            ')
            ->setParameter('now', new \DateTime());
    }

    public function queryNewsByCat($catSlug, $limit)
    {
        return $this
            ->createQueryBuilder('n')
            ->leftJoin('n.category', 'category')
            ->andWhere('category.slug = :cat')
            ->setParameter('cat', $catSlug)
            ->setMaxResults($limit)
            ->orderBy('n.created', 'DESC')
            ->getQuery()->getResult();
    }

    /**
     * @param int $page
     *
     * @return Pagerfanta
     */
    public function findLatest($page = 1)
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryLatest(), false));
        $paginator->setMaxPerPage(News::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}
