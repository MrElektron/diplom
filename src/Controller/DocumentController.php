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
use PHPWord_IOFactory;
use PhpOffice\PhpWord\PhpWord;

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
        $disciplines = $discipline = $this->getDisciplineRepository()->findBy(['cycle' => false, 'include' => false, 'file' => $documentId]);

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

        if ($discipline->getProfessionalModule()) {
            $document = new \PhpOffice\PhpWord\TemplateProcessor($this->getParameter('kernel.project_dir') . '/public/TemplateFiles/Professional module work program.docx');
            $competences = '';
            //Create table
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $styleCell =
                [
                    'borderColor' =>'000000',
                    'borderSize' => 6,
                    'valign'=>'center'
                ];
            $boldCell =
                [
                    'bold' =>true
                ];
            $boldCellItalic =
                [
                    'bold' =>true,
                    'italic' => true
                ];
            $table = $section->addTable();
            $table->addRow();
            $table->addCell(1200, $styleCell)->addText("Код", $boldCell);
            $table->addCell(9000, $styleCell, $boldCell)->addText("Наименование общих компетенций", $boldCellItalic);

            if ($disciplineCompetence) {
                foreach ($disciplineCompetence as $item) {
                    $competence = $item->getCompetence();
                    $name = $competence->getName();
                    $description = $competence->getDescription();
                    $competences .= $name .  ' ' . $description . '<w:br />';
                    if (mb_substr($name,0,2) == 'ОК') {
                        $table->addRow();
                        $table->addCell(1200, $styleCell)->addText($name, $boldCell);
                        $table->addCell(9000, $styleCell)->addText($description);
                    }
                }
            }

            if ($competences) {
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

                $fullxml = $objWriter->getWriterPart('Document')->write();

                $competencesTable = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);

            } else {
                $competences = '';
                $competencesTable = '';
            }

            $document->setValue('competences', $competences);
            $document->setValue('competencesTable', $competencesTable);

            $section = $phpWord->addSection();
            $table = $section->addTable();
            $table->addRow();
            $table->addCell(1200, $styleCell)->addText("Код", $boldCell);
            $table->addCell(9000, $styleCell, $boldCell)->addText("Наименование вида деятельности и профессиональных компетенций", $boldCellItalic);

            if ($disciplineCompetence) {
                foreach ($disciplineCompetence as $item) {
                    $competence = $item->getCompetence();
                    $name = $competence->getName();
                    $description = $competence->getDescription();
                    if (mb_substr($name,0,2) == 'ПК') {
                        $table->addRow();
                        $table->addCell(1200, $styleCell)->addText($name, $boldCell);
                        $table->addCell(9000, $styleCell)->addText($description);
                    }
                }
            }

            if ($competences) {
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

                $fullxml = $objWriter->getWriterPart('Document')->write();

                $competencesTable2 = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);

            } else {
                $competencesTable2 = '';
            }

            $document->setValue('competencesTable2', $competencesTable2);

            $section = $phpWord->addSection();
            $table = $section->addTable();
            $table->addRow();
            $table->addCell(1800, $styleCell)->addText("Код ПК/ ОК", $boldCell);
            $table->addCell(4200, $styleCell, $boldCell)->addText("Иметь практический опыт (ПО)");
            $table->addCell(2200, $styleCell, $boldCell)->addText("Уметь (У)");
            $table->addCell(2000, $styleCell, $boldCell)->addText("Знать (З)");

            if ($disciplineCompetence) {
                foreach ($disciplineCompetence as $item) {
                    $competence = $item->getCompetence();
                    $name = $competence->getName();
                    $table->addRow();;
                    $table->addCell(1800, $styleCell)->addText($name);
                    $table->addCell(4200, $styleCell, $boldCell)->addText("");
                    $table->addCell(2200, $styleCell, $boldCell)->addText("");
                    $table->addCell(2000, $styleCell, $boldCell)->addText("");
                }
            }

            if ($competences) {
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

                $fullxml = $objWriter->getWriterPart('Document')->write();

                $competencesTable3 = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);

            } else {
                $competencesTable3 = '';
            }

            $document->setValue('competencesTable3', $competencesTable3);
        } else {
            $document = new \PhpOffice\PhpWord\TemplateProcessor($this->getParameter('kernel.project_dir') . '/public/TemplateFiles/Discipline work program.docx');

            $competences = '';
            //Create table
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            $styleCell =
                [
                    'borderColor' =>'000000',
                    'borderSize' => 6,
                    'valign'=>'center'
                ];
            $boldCell =
                [
                    'bold' =>true
                ];
            $table = $section->addTable();
            $table->addRow();
            $table->addCell(2400, $styleCell)->addText("Код ОК", $boldCell);
            $table->addCell(3500, $styleCell, $boldCell)->addText("Умения", $boldCell);
            $table->addCell(3800, $styleCell, $boldCell)->addText("Знания", $boldCell);

            if ($disciplineCompetence) {
                foreach ($disciplineCompetence as $item) {
                    $competence = $item->getCompetence();
                    $name = $competence->getName();
                    $description = $competence->getDescription();

                    $competences .= $name .  ' ' . $description . '<w:br />';
                    $table->addRow();
                    $table->addCell(2400, $styleCell)->addText($name, $boldCell);
                    $table->addCell(3500, $styleCell)->addText($description);
                    $table->addCell(3800, $styleCell)->addText("");
                }
            }

            if ($competences) {
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

                $fullxml = $objWriter->getWriterPart('Document')->write();

                $competencesTable = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);

            } else {
                $competences = '';
                $competencesTable = '';
            }

            $document->setValue('competences', $competences);
            $document->setValue('competencesTable', $competencesTable);
        }

        $document->setValue('discipline', $discipline->getDisciplineIndex() . ' ' . mb_strtoupper($discipline->getName(), 'UTF-8'));
        if ($cycle) {
            $document->setValue('cycle', $cycle->getName());
        }
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

        $fileName = str_replace(',',' ','РП_' . $file->getCode() . '_' . $file->getQualification() . '_' . $discipline->getName() . '_' . date( 'Y' ) . ".docx");;

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
