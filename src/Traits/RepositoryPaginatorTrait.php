<?php
namespace AppBundle\Traits;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait RepositoryPaginatorTrait
{

    /**
     * @param QueryBuilder $qb
     * @param int $page
     * @param int $limit
     * @return Paginator
     */
    public function paginate($qb, $page = 1, $limit = 20)
    {
        $paginator = new Paginator($qb);

        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
        ;

        return $paginator;
    }
}