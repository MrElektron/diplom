<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DisciplineRepository")
 */
class Semester
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
     * @ORM\Column(type="integer")
     */
    private $semester;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maximum_load;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $independent_work;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $consultations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $obligatory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lessons;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $practical_lessons;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $laboratory_classes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $course_design;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $intermediate_certification;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lesson_workshop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $individual_project;

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

    public function getSemester(): ?int
    {
        return $this->semester;
    }

    public function setSemester(int $semester): self
    {
        $this->semester = $semester;

        return $this;
    }

    public function getMaximumLoad(): ?string
    {
        return $this->maximum_load;
    }

    public function setMaximumLoad(?string $maximum_load): self
    {
        $this->maximum_load = $maximum_load;

        return $this;
    }

    public function getIndependentWork(): ?string
    {
        return $this->independent_work;
    }

    public function setIndependentWork(?string $independent_work): self
    {
        $this->independent_work = $independent_work;

        return $this;
    }

    public function getConsultations(): ?string
    {
        return $this->consultations;
    }

    public function setConsultations(?string $consultations): self
    {
        $this->consultations = $consultations;

        return $this;
    }

    public function getObligatory(): ?string
    {
        return $this->obligatory;
    }

    public function setObligatory(?string $obligatory): self
    {
        $this->obligatory = $obligatory;

        return $this;
    }

    public function getLessons(): ?string
    {
        return $this->lessons;
    }

    public function setLessons(?string $lessons): self
    {
        $this->lessons = $lessons;

        return $this;
    }

    public function getPracticalLessons(): ?string
    {
        return $this->practical_lessons;
    }

    public function setPracticalLessons(?string $practical_lessons): self
    {
        $this->practical_lessons = $practical_lessons;

        return $this;
    }

    public function getLaboratoryClasses(): ?string
    {
        return $this->laboratory_classes;
    }

    public function setLaboratoryClasses(?string $laboratory_classes): self
    {
        $this->laboratory_classes = $laboratory_classes;

        return $this;
    }

    public function getCourseDesign(): ?string
    {
        return $this->course_design;
    }

    public function setCourseDesign(?string $course_design): self
    {
        $this->course_design = $course_design;

        return $this;
    }

    public function getIntermediateCertification(): ?string
    {
        return $this->intermediate_certification;
    }

    public function setIntermediateCertification(?string $intermediate_certification): self
    {
        $this->intermediate_certification = $intermediate_certification;

        return $this;
    }

    public function getLessonWorkshop(): ?string
    {
        return $this->lesson_workshop;
    }

    public function setLessonWorkshop(?string $lesson_workshop): self
    {
        $this->lesson_workshop = $lesson_workshop;

        return $this;
    }

    public function getIndividualProject(): ?string
    {
        return $this->individual_project;
    }

    public function setIndividualProject(?string $individual_project): self
    {
        $this->individual_project = $individual_project;

        return $this;
    }
}
