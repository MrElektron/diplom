<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Entity\DisciplineCompetence;
use App\Entity\User;
use App\Entity\Discipline;
use App\Entity\Semester;
use App\Entity\Specialty;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\File;
use App\Repository\RepositoryAwareTrait;
use App\Service\Export\DocumentBuilder;
use PHPExcel_IOFactory;

class DashboardController extends AbstractController
{
    use RepositoryAwareTrait;

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/", name="homepage")
     */
    public function homepageAction(Request $request)
    {
        $files = $this->getFileRepository()->findBy(['owner' => $this->getUser()]);
        $disciplines = $this->getDisciplineRepository()->findAll();

        return $this->render('dashboard/index.html.twig', [
            'files' => $files,
            'discipline' => $disciplines
        ]);
    }

    /**
     * Upload file api.
     *
     * @Route("/upload-file", name="upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $file = $request->files->get('file');
        if ($this->getFileRepository()->findOneBy(['fileName' => $file[0]->getClientOriginalName()])) {
            echo 'Файл с таким именем уже загружен!';
        } else {
            $this->uploadFile($file);
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param $files
     */
    protected function uploadFile($files)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();
        foreach ($files as $file) {
            try {
                $this->validateFile($file);
                $this->processFile($file);
            } catch (\Exception $exception) {
                $flashbag->add('danger', $exception->getMessage());
            }
        }
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function validateFile(UploadedFile $file)
    {
        if ($file->getSize() > 102400000) {
//            throw new MaxFileSizeException($this->get('translator'), $file->getClientOriginalName());
        } elseif($file->getClientOriginalExtension() != 'xls' && $file->getClientOriginalExtension() != 'xlsx') {
            throw new \Exception('Неверное расширение файла, к загрузке разрешены файлы с расшинением xls или xlsx!');
        }
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function processFile($file)
    {
        if ($file instanceof UploadedFile) {
            /** @var User $user */
            $user = $this->getUser();

            $format = !(empty($file->guessExtension()))
                ? $file->guessExtension()
                : $file->getClientOriginalExtension();

            $projectFile = new File();
            $projectFile = $this->buildProjectFile($projectFile, $file, $user, $format);

            $this->moveFile($file, $projectFile);
        }
    }

    /**
     * @param File $projectFile
     * @param UploadedFile $file
     * @param User $user
     * @param string $format
     * @return File
     */
    protected function buildProjectFile(File $projectFile, UploadedFile $file, User $user, string $format)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        $projectFile
            ->setUploadedAt(new \DateTime())
            ->setOwner($user)
            ->setFileName($file->getClientOriginalName())
            ->setFormat($format)
            ->setFileSize($file->getSize())
        ;

        try{
            $excelReader = PHPExcel_IOFactory::createReaderForFile($file);
            $excelObj = $excelReader->load($file);

            $worksheet = $excelObj->getSheet(0);
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
            var_dump($exception->getMessage());
            die;
        }

        if ($worksheet->getCellByColumnAndRow(0, 14)->getValue() != 'по специальности среднего профессионального образования') {
            $correctionIndex = 0;
        } else {
            $correctionIndex = 1;
        }
        $code = $worksheet->getCellByColumnAndRow(0, 14 + $correctionIndex)->getValue();
        $name = $worksheet->getCellByColumnAndRow(6, 14 + $correctionIndex)->getValue();
        $qualification = $worksheet->getCellByColumnAndRow(6, 19 + $correctionIndex)->getValue();
        $number = 'от ' . $worksheet->getCellByColumnAndRow(13, 32 + $correctionIndex)->getValue() . '., №' . $worksheet->getCellByColumnAndRow(20, 32 + $correctionIndex)->getValue();
        $educationForm = $worksheet->getCellByColumnAndRow(6, 27 + $correctionIndex)->getValue();

        if ($code and $name and $number) {
            $projectFile
                ->setCode($code)
                ->setName($name)
                ->setNumber($number)
                ->setQualification($qualification)
                ->setTrainingForm($educationForm)
            ;
        }

        return $projectFile;
    }

    /**
     * @param UploadedFile $file
     * @param File $projectFile
     * @return string
     * @throws \Exception
     */
    protected function moveFile(UploadedFile $file, File $projectFile)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $projectFile->setStoredFileDir($dirName);

        $basePath = 'files/' . $dirName;
        // Move the file to the directory where brochures are stored
        $file->move(
            $basePath,
            $storedFileName
        );

        try{
            $projectDir = $this->getParameter('kernel.project_dir') . '/public/';
            $fullPath = $projectDir . $basePath . '/' . $storedFileName;

            $this->getEm()->persist($projectFile);
            $this->getEm()->flush();

            $this->getDataOnDisciplines($fullPath, $projectFile);
            $this->getDataOnSemesters($fullPath);
            $this->getDataOnCompetence($fullPath,  $projectFile);
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
            var_dump($exception->getMessage());
            die;
        }
    }

    public function getDataOnSemesters($fileName)
    {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        try{
            $excelReader = PHPExcel_IOFactory::createReaderForFile($fileName);
            $excelObj = $excelReader->load($fileName);

            $worksheet = $excelObj->getSheet(2);
            $lastRow = $worksheet->getHighestRow();
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
            var_dump($exception->getMessage());
            die;
        }

        $em = $this->getEm();

        $column = 20;
        do {
            $semesterNumber = $worksheet->getCellByColumnAndRow($column, 3)->getValue();
            $semesterNumber = substr($semesterNumber, -1);
            if (is_numeric($semesterNumber)) {
                for ($row = 10; $row <= $lastRow; $row++) {
                    $index = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $maximumLoad = $worksheet->getCellByColumnAndRow($column, $row)->getValue();
                    $independentWork = $worksheet->getCellByColumnAndRow($column + 1, $row)->getValue();
                    $consultations = $worksheet->getCellByColumnAndRow($column + 2, $row)->getValue();
                    $obligatory = $worksheet->getCellByColumnAndRow($column + 3, $row)->getValue();
                    $lessons = $worksheet->getCellByColumnAndRow($column + 4, $row)->getValue();
                    $practicalLessons = $worksheet->getCellByColumnAndRow($column + 5, $row)->getValue();
                    $laboratoryClasses = $worksheet->getCellByColumnAndRow($column + 6, $row)->getValue();
                    $lessonWorkshop = $worksheet->getCellByColumnAndRow($column + 7, $row)->getValue();
                    $courseDesign = $worksheet->getCellByColumnAndRow($column + 8, $row)->getValue();
                    $intermediateCertification = $worksheet->getCellByColumnAndRow($column + 9, $row)->getValue();
                    $individualProject = $worksheet->getCellByColumnAndRow($column + 10, $row)->getValue();

                    if ($index == "ПЦ") {
                        break;
                    }

                    if ($index and mb_strlen($index) >= 5 and is_numeric(substr($index, -1))and $name) {
                            $semester = new Semester();

                            $semester
                                ->setDiscipline($this->getDisciplineRepository()->findOneBy(['discipline_index' => $index]))
                                ->setSemester($semesterNumber)
                                ->setMaximumLoad($maximumLoad)
                                ->setIndependentWork($independentWork)
                                ->setConsultations($consultations)
                                ->setObligatory($obligatory)
                                ->setLessons($lessons)
                                ->setPracticalLessons($practicalLessons)
                                ->setLaboratoryClasses($laboratoryClasses)
                                ->setLessonWorkshop($lessonWorkshop)
                                ->setCourseDesign($courseDesign)
                                ->setIntermediateCertification($intermediateCertification)
                                ->setIndividualProject($individualProject);

                            $em->persist($semester);
                    }
                }
            }
            $column+=11;
        } while (is_numeric($semesterNumber));

        $em->flush();

    }

    public function getDataOnDisciplines($fileName, $file) {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        try{
        $excelReader = PHPExcel_IOFactory::createReaderForFile($fileName);
        $excelObj = $excelReader->load($fileName);

        $worksheet = $excelObj->getSheet(2);
        $lastRow = $worksheet->getHighestRow();
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
            var_dump($exception->getMessage());
            die;
        }

        $em = $this->getEm();

        for ($row = 10; $row <= $lastRow; $row++) {
            $index = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
            $name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $exams = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
            $offsets = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
            $differentiatedOffsets = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
            $courseProjects = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
            $coursework = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
            $other = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
            $maximumLoad = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
            $independentWork = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
            $consultations = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
            $total = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
            $lessons = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
            $practicalLessons = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
            $laboratoryClasses = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
            $lessonWorkshop = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
            $courseDesign = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
            $intermediateCertification = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
            $individualProject = $worksheet->getCellByColumnAndRow(19, $row)->getValue();

            if ($index == "ПЦ") {
                break;
            }

            if (($index and mb_strlen($index) >= 5 and is_numeric(substr($index, -1)) and $name)
            or ($index == "БД" or $index == "ПД" or $index == "ПОО" or substr($name, -8) == "цикл")) {
                $discipline = new Discipline();

                $discipline
                    ->setFile($file)
                    ->setDisciplineIndex($index)
                    ->setName($name)
                    ->setExams($exams)
                    ->setOffsets($offsets)
                    ->setDifferentiatedOffsets($differentiatedOffsets)
                    ->setCourseProjects($courseProjects)
                    ->setCoursework($coursework)
                    ->setOther($other)
                    ->setMaximumLoad($maximumLoad)
                    ->setIndependentWork($independentWork)
                    ->setConsultations($consultations)
                    ->setTotal($total)
                    ->setLessons($lessons)
                    ->setPracticalLessons($practicalLessons)
                    ->setLaboratoryClasses($laboratoryClasses)
                    ->setLessonWorkshop($lessonWorkshop)
                    ->setCourseDesign($courseDesign)
                    ->setIntermediateCertification($intermediateCertification)
                    ->setIndividualProject($individualProject)
                ;
                if ($index == "БД" or $index == "ПД" or $index == "ПОО" or substr($name, -8) == "цикл") {
                    $discipline->setCycle(true);
                }

                $em->persist($discipline);
            }
        }

        $em->flush();
    }

    public function getDataOnCompetence($fileName, $file) {
        $flashbag = $this->get('session')->getFlashBag();
        $flashbag->clear();

        try{
            $excelReader = PHPExcel_IOFactory::createReaderForFile($fileName);
            $excelObj = $excelReader->load($fileName);

            $worksheet = $excelObj->getSheet(4);
            $lastRow = $worksheet->getHighestRow();
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
            var_dump($exception->getMessage());
            die;
        }

        $em = $this->getEm();

        for ($row = 2; $row <= $lastRow; $row++) {
            $name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
            $description = $worksheet->getCellByColumnAndRow(4, $row)->getValue();

            if ($name and $description) {
                $competence = new Competence();

                $competence
                    ->setName($name)
                    ->setDescription($description)
                    ->setFile($file)
                ;

                $em->persist($competence);
            }
        }

        $em->flush();

        $worksheet = $excelObj->getSheet(5);

        for ($row = 0; $row <= $lastRow; $row++) {
            $index = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            if ($index == "ПЦ") {
                break;
            }

            $discipline = $this->getDisciplineRepository()->findOneBy(['discipline_index' => $index, 'file' => $file]);
            if ($discipline instanceof Discipline) {
                $i = 0;
                while ($worksheet->getCellByColumnAndRow(5 + $i, $row)->getValue()) {
                    $competence = $worksheet->getCellByColumnAndRow(5 + $i, $row)->getValue();
                    $competence = $this->getCompetenceRepository()->findOneBy(['name' => $competence, 'file' => $file]);
                    $disciplineCompetence = new DisciplineCompetence();

                    $disciplineCompetence
                        ->setDiscipline($discipline)
                        ->setCompetence($competence)
                    ;

                    $em->persist($disciplineCompetence);

                    $i++;
                }
            }
        }
        $em->flush();
    }


}
