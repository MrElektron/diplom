<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\Discipline;
use App\Entity\Semester;
use App\Entity\Competence;
use App\Entity\DisciplineCompetence;
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
     * @return CompetenceRepository
     */
    protected function getCompetenceRepository()
    {
        return $this->getDoctrine()->getRepository(Competence::class);
    }

    /**
     * @return DisciplineCompetenceRepository
     */
    protected function getDisciplineCompetenceRepository()
    {
        return $this->getDoctrine()->getRepository(DisciplineCompetence::class);
    }
}