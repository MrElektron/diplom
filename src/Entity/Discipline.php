<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DisciplineRepository")
 */
class Discipline
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $discipline_index;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exams;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $offsets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $differentiated_offsets;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $course_projects;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coursework;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $other;

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
    private $total;

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

    /**
     * @ORM\Column(type="boolean")
     */
    private $cycle = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $professional_module = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $include = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getDisciplineIndex(): ?string
    {
        return $this->discipline_index;
    }

    public function setDisciplineIndex(string $discipline_index): self
    {
        $this->discipline_index = $discipline_index;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExams(): ?string
    {
        return $this->exams;
    }

    public function setExams(?string $exams): self
    {
        $this->exams = $exams;

        return $this;
    }

    public function getOffsets(): ?string
    {
        return $this->offsets;
    }

    public function setOffsets(?string $offsets): self
    {
        $this->offsets = $offsets;

        return $this;
    }

    public function getDifferentiatedOffsets(): ?string
    {
        return $this->differentiated_offsets;
    }

    public function setDifferentiatedOffsets(?string $differentiated_offsets): self
    {
        $this->differentiated_offsets = $differentiated_offsets;

        return $this;
    }

    public function getCourseProjects(): ?string
    {
        return $this->course_projects;
    }

    public function setCourseProjects(?string $course_projects): self
    {
        $this->course_projects = $course_projects;

        return $this;
    }

    public function getCoursework(): ?string
    {
        return $this->coursework;
    }

    public function setCoursework(?string $coursework): self
    {
        $this->coursework = $coursework;

        return $this;
    }

    public function getOther(): ?string
    {
        return $this->other;
    }

    public function setOther(?string $other): self
    {
        $this->other = $other;

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

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): self
    {
        $this->total = $total;

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

    public function getCycle(): ?bool
    {
        return $this->cycle;
    }

    public function setCycle(?bool $cycle): self
    {
        $this->cycle = $cycle;

        return $this;
    }

    public function getProfessionalModule(): ?bool
    {
        return $this->professional_module;
    }

    public function setProfessionalModule(?bool $professional_module): self
    {
        $this->professional_module = $professional_module;

        return $this;
    }

    public function getInclude(): ?bool
    {
        return $this->include;
    }

    public function setInclude(?bool $include): self
    {
        $this->include = $include;

        return $this;
    }
}
