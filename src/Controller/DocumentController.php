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

class DocumentController extends AbstractController
{
    use RepositoryAwareTrait;

    /**
     * @Route("/document/{documentId}/details", name="document_details")
     */
    public function detailsAction(Request $request)
    {
        $documentId = $request->get('documentId');

        /** @var File $document */
        $document = $this->getFileRepository()->find($documentId);
        $disciplines = $discipline = $this->getDisciplineRepository()->findAll();

        return $this->render('document/details.html.twig', [
            'document' => $document,
            'disciplines' => $disciplines
        ]);
    }

    /**
     * @Route("/export-file/{id}", name="export_file")
     */
    public function exportFileAction(Request $request)
    {
        $fileId = $request->get('id');
        $disciplineId = $request->get('discipline');
        /** @var File $file */
        $file = $this->getFileRepository()->find($fileId);

        /** @var Discipline $discipline */
        $discipline = $this->getDisciplineRepository()->findOneBy(['id' => $disciplineId]);
        $specialty = $this->getSpecialtyRepository()->findOneBy(['file' => $file]);

        $document = new \PhpOffice\PhpWord\TemplateProcessor($this->getParameter('kernel.project_dir') . '/public/TemplateFiles/word.docx');

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

        $fileName = 'РП_' . $specialty->getCode() . '_' . $specialty->getQualification() . '_' . $discipline->getName() . '_' . date( 'Y' ) . ".docx";
        $document->saveAs('files/' . $fileName);

        $headers = [
            'Content-Type' => 'application/docx',
            'Content-Disposition' => "inline; filename=$fileName"
        ];

        $response = new Response(file_get_contents('files/' . $fileName), 200, $headers);

        unlink('files/' . $fileName);

        return $response;
    }

}
