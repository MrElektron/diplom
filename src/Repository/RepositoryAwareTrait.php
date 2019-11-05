<?php

namespace App\Repository;

use App\Entity\File;
use App\Repository\FileRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ManagerRegistry;

Trait RepositoryAwareTrait
{
//    /**
//     * @return Registry
//     */
//    abstract protected function getDoctrine();

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return FileRepository
     */
    protected function getFileRepository()
    {
        return $this->getDoctrine()->getRepository(File::class);
    }
}