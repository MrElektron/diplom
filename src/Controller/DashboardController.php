<?php

namespace App\Controller;

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
        return $this->render('dashboard/index.html.twig', []);
    }

    /**
     * Upload file api.
     *
     * @Route("/upload-file", name="upload_file")
     */
    public function uploadFileAction(Request $request)
    {
        $file = $request->files->get('file');
        $this->uploadFile($file);

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
        }
    }

    /**
     * @param UploadedFile $file
     * @throws \Exception
     */
    protected function processFile($file)
    {
        if ($file instanceof UploadedFile) {
            $projectFile = new File();
            $projectFile = $this->buildProjectFile($projectFile);

            $format = !(empty($file->guessExtension()))
                ? $file->guessExtension()
                : $file->getClientOriginalExtension();

            $projectFile
                ->setFileName($file->getClientOriginalName())
                ->setFormat($format)
                ->setFileSize($file->getSize())
            ;

            $this->moveFile($file, $projectFile);

            $this->getEm()->persist($projectFile);
            $this->getEm()->flush();
        }
    }

    /**
     * @param File $file
     * @return File
     */
    protected function buildProjectFile(File $file)
    {
        $file
            ->setUploadedAt(new \DateTime())
        ;

        return $file;
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
        $projectFile->setStoredFileName($storedFileName);
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
            $this->getDataOnDisciplines($fullPath);
        } catch (\Exception $exception) {
            $flashbag->add('danger', $exception->getMessage());
            var_dump($exception->getMessage());
            die;
        }
    }

    /**
     * @Route("/export-file/{id}", name="export_file")
     */
    public function exportFileAction(Request $request)
    {
        $fileId = $request->get('id');
        /** @var File $document */
        $file = $this->getFileRepository()->find($fileId);

        $exportBuilder = new DocumentBuilder();
        $phpWordObject = $exportBuilder->build($file);

        $filename = 'File.docx';
//        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWordObject, 'Word2007');

        $tmp = tempnam('', 'document');

        $writer->save($tmp);

        $headers = [
            'Content-Type' => 'application/docx',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ];

        $response = new Response(file_get_contents($tmp), 200, $headers);

        unlink($tmp);

        return $response;
    }

//    public function unloadWord($dbh) {
//        $document = new \PhpOffice\PhpWord\TemplateProcessor('TemplateFiles/Шаблон.docx');
//
//        $sql = $dbh->query('SELECT * FROM discipline');
//        $disciplines = $sql->fetchAll();
//        $discipline = $disciplines[1];
//
//        $sql = $dbh->query('SELECT * FROM specialty');
//        $specialty = $sql->fetchAll();
//        $specialty = $specialty[0];
//
//        $document->setValue('discipline', $discipline['discipline_index'] . ' ' . mb_strtoupper($discipline['name'], 'UTF-8'));
//        $document->setValue('code', $specialty['code'] . ' ' . $specialty['name']);
//        $document->setValue('qualification', $specialty['qualification']);
//        $document->setValue('registrationNumber', '09.02.07-170511');
//        $document->setValue('shortDiscipline', $discipline['name']);
//        $document->setValue('shortDisciplineUpper', mb_strtoupper($discipline['name'], 'UTF-8'));
//
//        $document->setValue('total', $discipline['total']);
//        $document->setValue('lessons', $discipline['lessons']);
//        $document->setValue('practicalLessons', $discipline['practical_lessons']);
//        $document->setValue('laboratoryClasses', $discipline['laboratory_classes']);
//        $document->setValue('courseDesign', $discipline['course_design']);
//        $document->setValue('consultations', $discipline['consultations']);
//        $document->setValue('lessonWorkshop', $discipline['lesson_workshop']);
//        $document->setValue('intermediateCertification', $discipline['intermediate_certification']);
//
//        $document->saveAs('Files\РП_ЭВМ_09.02.07_Программист_2018.docx');
//    }

    public function getDataOnDisciplines($fileName) {
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
        $em->createQuery('DELETE FROM App\Entity\Semester')->execute();


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

                    if ($index and strlen($index) >= 5 and !is_numeric($index) and $name) {
                        $semester = new Semester();

                        $semester
                            ->setDiscipline($index)
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
                            ->setIndividualProject($individualProject)
                        ;

                        $em->persist($semester);
                    }
                }
            }
            $column+=11;
        } while (is_numeric($semesterNumber));

        $em->createQuery('DELETE FROM App\Entity\Discipline')->execute();

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

            if ($index and strlen($index) >= 5 and !is_numeric($index) and $name) {
                $discipline = new Discipline();

                $discipline
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

                $em->persist($discipline);
            }
        }

        $worksheet = $excelObj->getSheet(0);

        $em->createQuery('DELETE FROM App\Entity\Specialty')->execute();

        $code = $worksheet->getCellByColumnAndRow(0, 14)->getValue();
        $name = $worksheet->getCellByColumnAndRow(6, 14)->getValue();
        $qualification = $worksheet->getCellByColumnAndRow(6, 19)->getValue();
        $number = $worksheet->getCellByColumnAndRow(20, 32)->getValue();

        if ($code and $name and $number) {
            $specialty = new Specialty();

            $specialty
                ->setCode($code)
                ->setName($name)
                ->setNumber($number)
                ->setQualification($qualification)
            ;

            $em->persist($specialty);
        }

        $em->flush();
    }
}
