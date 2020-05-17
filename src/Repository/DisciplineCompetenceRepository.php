<?php

namespace App\Repository;

use App\Entity\Competence;
use App\Entity\DisciplineCompetence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Competence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Competence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Competence[]    findAll()
 * @method Competence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineCompetenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DisciplineCompetence::class);
    }


    /**
     * @param $discipline
     * @param $file
     */
    public function getFiles($discipline, $file)
    {
        $qb = $this->createQueryBuilder('dc');

        $qb
            ->select('dc')
            ->andWhere('dc.discipline = :disciplineId')
            ->setParameter('disciplineId', $discipline)
            ->leftJoin('dc.discipline', 'd')
            ->andWhere('d.file = :fileId')
            ->setParameter('fileId', $file);

        return $qb->getQuery()->getResult();
    }
}
