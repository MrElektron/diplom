<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\Discipline;
use App\Entity\Semester;
use App\Entity\Specialty;
use App\Entity\User;

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

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getDoctrine()->getRepository(User::class);
    }

    /**
     * @return DisciplineRepository
     */
    protected function getDisciplineRepository()
    {
        return $this->getDoctrine()->getRepository(Discipline::class);
    }

    /**
     * @return SemesterRepository
     */
    protected function getSemesterRepository()
    {
        return $this->getDoctrine()->getRepository(Semester::class);
    }

    /**
     * @return SpecialtyRepository
     */
    protected function getSpecialtyRepository()
    {
        return $this->getDoctrine()->getRepository(Specialty::class);
    }
}