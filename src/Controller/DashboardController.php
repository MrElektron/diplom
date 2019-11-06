<?php

namespace App\Controller;

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
        $files = $this->getFileRepository()->findAll();

        return $this->render('dashboard/index.html.twig', [
            'files' => $files
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

            $projectFile = new File();
            $projectFile = $this->buildProjectFile($projectFile, $user);

            $format = !(empty($file->guessExtension()))
                ? $file->guessExtension()
                : $file->getClientOriginalExtension();

            $projectFile
                ->setFileName($file->getClientOriginalName())
                ->setFormat($format)
                ->setFileSize($file->getSize())
            ;

            $this->moveFile($file, $projectFile);
        }
    }

    /**
     * @param File $file
     * @param User $user
     * @return File
     */
    protected function buildProjectFile(File $file, User $user)
    {
        $file
            ->setUploadedAt(new \DateTime())
            ->setOwner($user)
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

            $this->getEm()->persist($projectFile);
            $this->getEm()->flush();

            $this->getDataOnDisciplines($fullPath, $projectFile);
            $this->getDataOnSemesters($fullPath);
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

        $document = new \PhpOffice\PhpWord\TemplateProcessor($this->getParameter('kernel.project_dir') . '/public/TemplateFiles/word.docx');

        $disciplines = $this->getDisciplineRepository()->findAll();
        $discipline = $disciplines[1];

        $specialties = $this->getSpecialtyRepository()->findAll();
        $specialty = $specialties[0];

        $document->setValue('discipline', $discipline->getDisciplineIndex() . ' ' . mb_strtoupper($discipline->getName(), 'UTF-8'));
        $document->setValue('code', $specialty->getCode() . ' ' . $specialty->getName());
        $document->setValue('number', $specialty->getNumber());
        $document->setValue('qualification', $specialty->getQualification());
        $document->setValue('registrationNumber', $specialty->getNumber());
        $document->setValue('shortDiscipline', $discipline->getName());
        $document->setValue('shortDisciplineUpper', mb_strtoupper($discipline->getName(), 'UTF-8'));

        $document->setValue('total', $discipline->getTotal());
        $document->setValue('lessons', ($discipline->getLessons() ? $discipline->getLessons() : 'Не предусмотрено'));
        $document->setValue('practicalLessons', ($discipline->getPracticalLessons() ? $discipline->getPracticalLessons() : 'Не предусмотрено'));
        $document->setValue('laboratoryClasses', ($discipline->getLaboratoryClasses() ? $discipline->getLaboratoryClasses() : 'Не предусмотрено'));
        $document->setValue('courseDesign', ($discipline->getCourseDesign() ? $discipline->getCourseDesign() : 'Не предусмотрено'));
        $document->setValue('consultations', ($discipline->getConsultations() ? $discipline->getConsultations() : 'Не предусмотрено'));
        $document->setValue('lessonWorkshop', ($discipline->getLessonWorkshop() ? $discipline->getLessonWorkshop() : 'Не предусмотрено'));
        $document->setValue('intermediateCertification', ($discipline->getIntermediateCertification() ? $discipline->getIntermediateCertification() : 'Дифф. зачёт'));

        $fileName = 'РП_' . $specialty->getCode() . '_' . $specialty->getQualification() . '_' . date( 'Y' ) . ".docx";
        $document->saveAs('files/' . $fileName);

        $headers = [
            'Content-Type' => 'application/docx',
            'Content-Disposition' => "inline; filename=$fileName"
        ];

        $response = new Response(file_get_contents('files/' . $fileName), 200, $headers);

        unlink('files/' . $fileName);

        return $response;
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

                    if ($index and mb_strlen($index) >= 5 and is_numeric(substr($index, -1))and $name
                        and $index != 'ПМ.01' and $index != 'ПМ.02' and $index != 'ПМ.04' and $index != 'ПМ.11' ) {
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
                            ->setIndividualProject($individualProject)
                        ;

                        $em->persist($semester);
                    }
                }
            }
            $column+=11;
        } while (is_numeric($semesterNumber));

        $em->flush();

    }

    public function getDataOnDisciplines($fileName, File $file) {
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
//        $em->createQuery('DELETE FROM App\Entity\Discipline')->execute();

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

            if ($index and mb_strlen($index) >= 5 and is_numeric(substr($index, -1))and $name
                and $index != 'ПМ.01' and $index != 'ПМ.02' and $index != 'ПМ.04' and $index != 'ПМ.11' ) {
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

                $em->persist($discipline);
            }
        }

        $worksheet = $excelObj->getSheet(0);

//        $em->createQuery('DELETE FROM App\Entity\Specialty')->execute();

        $code = $worksheet->getCellByColumnAndRow(0, 14)->getValue();
        $name = $worksheet->getCellByColumnAndRow(6, 14)->getValue();
        $qualification = $worksheet->getCellByColumnAndRow(6, 19)->getValue();
        $number = 'от ' . $worksheet->getCellByColumnAndRow(13, 32)->getValue() . '., №' . $worksheet->getCellByColumnAndRow(20, 32)->getValue();

        if ($code and $name and $number) {
            $specialty = new Specialty();

            $specialty
                ->setFile($file)
                ->setCode($code)
                ->setName($name)
                ->setNumber($number)
                ->setQualification($qualification)
            ;

            $em->persist($specialty);
        }

        $worksheet = $excelObj->getSheet(5);

//        $em->createQuery('DELETE FROM App\Entity\Specialty')->execute();
//
//        $code = $worksheet->getCellByColumnAndRow(0, 14)->getValue();
//        $name = $worksheet->getCellByColumnAndRow(6, 14)->getValue();
//        $qualification = $worksheet->getCellByColumnAndRow(6, 19)->getValue();
//        $number = 'от ' . $worksheet->getCellByColumnAndRow(13, 32)->getValue() . '., №' . $worksheet->getCellByColumnAndRow(20, 32)->getValue();
//
//        if ($code and $name and $number) {
//            $specialty = new Specialty();
//
//            $specialty
//                ->setCode($code)
//                ->setName($name)
//                ->setNumber($number)
//                ->setQualification($qualification)
//            ;
//
//            $em->persist($specialty);
//        }

        $em->flush();
    }
}
