<?php

namespace App\Service\Export;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

class DocumentBuilder
{
    /**
     * DocumentCardBuilder constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $file
     * @return PhpWord
     */
    public function build($file)
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        $properties = $phpWord->getDocInfo();
//        $properties->setCreator($document->getOwner());
        $properties->setCreator('MrElektron');
        $properties->setCompany('Андроидная техника');
        $properties->setLastModifiedBy('Olymp');
        $properties->setTitle('Выгрузка');
        $properties->setDescription('Документ');
        $properties->setSubject('Документ');

        $section = $phpWord->addSection();

        $title = 'Заголовок';
        $section->addText(htmlspecialchars_decode(trim(strip_tags($title))), ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

        $section->addTextBreak();

        return $phpWord;
    }
}