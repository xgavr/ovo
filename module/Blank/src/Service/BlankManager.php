<?php

namespace Blank\Service;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Zend\ServiceManager\ServiceManager;

use MvlabsPHPExcel\Service\PhpExcelService;
/**
 * Description of BlankManager
 *
 * @author Daddy
 */
class BlankManager {
    //put your code here
    
    public function __construct()
    {
    }

    public function test()
    {
        $phpExcelService = new PhpExcelService();
        $objPHPExcel = $phpExcelService->createPHPExcelObject();
        $objPHPExcel->getProperties()->setCreator("Diego Drigani")
            ->setLastModifiedBy("Diego Drigani")
            ->setTitle("MvlabsPHPExcel Test Document")
            ->setSubject("MvlabsPHPExcel Test Document")
            ->setDescription("Test document for MvlabsPHPExcel, generated using Zend Framework 2 and PHPExcel.")
            ->setKeywords("office PHPExcel php zf2 mvlabs")
            ->setCategory("Test result file");
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!');
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A4', 'Miscellaneous glyphs');
           // ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

        $objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
        $objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
        $objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setTitle('Mvlabs');
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = $phpExcelService->createWriter($objPHPExcel, 'Excel2007' );
        
        $objWriter->save('D:\Users\Daddy\Downloads\myTest.xlsx');

        $response = $phpExcelService->createHttpResponse($objWriter, 200, [
            'Pragma' => 'public',
            'Cache-control' => 'must-revalidate, post-check=0, pre-check=0',
            'Cache-control' => 'private',
            'Expires' => '0000-00-00',
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=' . 'myTest.xls',
            ]);

        return $response;    
    }
}
