<?php

namespace App\Repository;

use App\Entity\Discipline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Discipline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discipline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discipline[]    findAll()
 * @method Discipline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discipline::class);
    }

    public function findCycle($value, $file)
    {
        return $this->createQueryBuilder('d')
            ->select('d')
            ->andWhere('d.discipline_index = :val')
            ->setParameter('val', substr($value,0, -3))
            ->andWhere('d.file = :fileId')
            ->setParameter('fileId', $file)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
