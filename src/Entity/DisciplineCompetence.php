<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DisciplineCompetenceRepository")
 */
class DisciplineCompetence
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Discipline")
     * @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
     */
    private $discipline;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Competence")
     * @ORM\JoinColumn(name="competence_id", referencedColumnName="id")
     */
    private $competence;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getCompetence(): ?Discipline
    {
        return $this->competence;
    }

    public function setCompetence(Competence $competence): self
    {
        $this->competence = $competence;

        return $this;
    }
}
