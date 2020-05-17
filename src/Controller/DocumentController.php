<?php

namespace App\Controller;

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
        $disciplines = $discipline = $this->getDisciplineRepository()->findBy(['cycle' => false, 'file' => $documentId]);

        return $this->render('document/details.html.twig', [
            'document' => $document,
            'disciplines' => $disciplines
        ]);
    }

    /**
     * @Route("/document/{documentId}", name="delete_document")
     */
    public function deleteFileAction(Request $request)
    {
        $documentId = $request->get('documentId');

        /** @var File $document */
        $document = $this->getFileRepository()->find($documentId);

        unlink('files/' . $document->getStoredFileDir() . '/' . $document->getFileName());
        rmdir('files/' . $document->getStoredFileDir());

        $em = $this->getEm();
        $em->remove($document);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
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
        $discipline = $this->getDisciplineRepository()->findOneBy(['id' => $disciplineId, 'file' => $fileId]);

        $disciplineCompetence = $this->getDisciplineCompetenceRepository()->getFiles($discipline, $fileId);

        $cycle = $this->getDisciplineRepository()->findCycle($discipline->getDisciplineIndex(), $fileId);

        $document = new \PhpOffice\PhpWord\TemplateProcessor($this->getParameter('kernel.project_dir') . '/public/TemplateFiles/word.docx');

        $i = 1;
        foreach ($disciplineCompetence as $item) {
            $competence = $item->getCompetence();

            $document->setValue('competence' . $i, $competence->getName());
            $document->setValue('competenceDescription' . $i, $competence->getDescription());

            $i++;
        }

        $document->setValue('discipline', $discipline->getDisciplineIndex() . ' ' . mb_strtoupper($discipline->getName(), 'UTF-8'));
        $document->setValue('cycle', $cycle->getName());
        $document->setValue('educationForm', $file->getTrainingForm());
        $document->setValue('code', $file->getCode() . ' ' . $file->getName());
        $document->setValue('number', $file->getNumber());
        $document->setValue('qualification', $file->getQualification());
        $document->setValue('registrationNumber', $file->getNumber());
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

        $fileName = 'РП_' . $file->getCode() . '_' . $file->getQualification() . '_' . $discipline->getName() . '_' . date( 'Y' ) . ".docx";
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
