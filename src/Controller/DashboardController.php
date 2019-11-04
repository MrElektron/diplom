<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\File;

class DashboardController extends AbstractController
{

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
            throw new MaxFileSizeException($this->get('translator'), $file->getClientOriginalName());
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

            $this->getDoctrine()->getManager()->persist($projectFile);
            $this->getDoctrine()->getManager()->flush();
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
        // Generate a unique name for the file before saving it
        $dirName = uniqid();
        $fileName = $file->getClientOriginalName();
        $storedFileName = $fileName;
        $projectFile->setStoredFileName($storedFileName);
        $projectFile->setStoredFileDir($dirName);

        $basePath = 'files/' . $dirName;
        // Move the file to the directory where brochures are stored
//        var_dump($basePath);
//        die;
        $file->move(
            $basePath,
            $storedFileName
        );
    }
}
