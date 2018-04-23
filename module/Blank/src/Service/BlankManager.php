<?php

namespace Blank\Service;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Zend\ServiceManager\ServiceManager;

use MvlabsPHPExcel\Service\PhpExcelService;
use Blank\Filter\DateMonthRuFilter;
use Blank\Filter\NumToStrRuFilter;
use Blank\Filter\mbUcfirstFilter;
/**
 * Description of BlankManager
 *
 * @author Daddy
 */
class BlankManager {
    //put your code here
    
    const TMP_DIR = './data/tmp';
    
    public function __construct()
    {
        if (!is_dir($this::TMP_DIR)){
            mkdir($this::TMP_DIR);
        }
        
    }
    
    public function invoice($source, $options = null)
    {

        $tmp_dir = './data/tmp';
        $tmpl_file = './data/templates/schet.xls';

        $data = [
            'firmName' => '',
            'firmAddress' => '',
            'firmInn' => '',
            'firmKpp' => '',
            'firmRs' => '',
            'firmBik' => '',
            'firmBank' => '',
            'firmKs' => '',
            'invoiceId' => 'б/н',
            'invoiceDate' => date('Y-m-d'),
            'clientName' => '',
            'clientConsignee' => '',
            'itemsTotal' => '',
            'total_without_tax' => '',
            'taxTotal' => '',
            'total' => '',
            'chief' => '',
            'chiefAccount' => '',
            'items' => [
                [
                    'goodName' => '',
                    'unit' => '',
                    'quantity' => '',
                    'price' => '',
                    'total' => '',
                ],    
            ],
        ];
        
        if (is_array($source)){
            $data = array_merge($data, $source);
        }
        
        if (is_array($data)){
            $dateFilter = new DateMonthRuFilter();
            $numFilter = new NumToStrRuFilter();
            $ucfirstFilter = new mbUcfirstFilter();
            
            $phpExcelService = new PhpExcelService();
            $objPHPExcel = $phpExcelService->createPHPExcelObject($tmpl_file);

            $objPHPExcel->getProperties()->setCreator("ovo.msk.ru")
                ->setLastModifiedBy("ovo.msk.ru")
                ->setTitle("Счет на оплату")
                ->setSubject("Счет на оплату")
                ->setDescription("Счета на оплату, сгенерированный на сайте ovo.msk.ru")
                ->setKeywords("Счет на оплату")
                ->setCategory("Счет на оплату");

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B1', $data['firmName'])
                ->setCellValue('B3', $data['firmAddress'])
                ->setCellValue('B6', 'ИНН '.$data['firmInn'])
                ->setCellValue('D6', 'КПП '.$data['firmKpp'])
                ->setCellValue('B8', $data['firmName'])
                ->setCellValue('G8', $data['firmRs'])
                ->setCellValue('G9', $data['firmBik'])
                ->setCellValue('B10', $data['firmBank'])
                ->setCellValue('G10', $data['firmKs'])
                ->setCellValue('B12', 'Счет №'.$data['invoiceId'].' от '.$dateFilter->filter($data['invoiceDate']))
                ->setCellValue('B14', 'Плательщик:      '.$data['clientName'])
                ->setCellValue('B15', 'Грузополучатель: '.$data['clientConsignee'])
                ->setCellValue('H20', $data['total_without_tax'])                    
                ->setCellValue('H21', $data['taxTotal'])                    
                ->setCellValue('H22', $data['total'])                    
                ->setCellValue('B24', 'Всего наименований '.count($data['items']).', на сумму '.number_format((float) $data['total'], 2, ' руб. ', ' '). ' коп.')
                ->setCellValue('B25', $ucfirstFilter->filter($numFilter->filter($data['total'])))
                ->setCellValue('B27', 'Руководитель предприятия  _____________________  ('.$data['chief'].')')
                ->setCellValue('B29', 'Главный бухгалтер         _____________________  ('.$data['chiefAccount'].')')                    
                    ;
            
            $i = 19; //начальная строка таблицы
            

            if (count($data['items'])>1){
                $objPHPExcel->getActiveSheet()->insertNewRowBefore($i, count($data['items'])); 
            }
            
            $sourceRow = $i + count($data['items']);
            $pCellStyle =[
                'B' => $objPHPExcel->getActiveSheet()->getStyle("B$sourceRow"),
                'C' => $objPHPExcel->getActiveSheet()->getStyle("C$sourceRow"),
                'E' => $objPHPExcel->getActiveSheet()->getStyle("E$sourceRow"),
                'F' => $objPHPExcel->getActiveSheet()->getStyle("F$sourceRow"),
                'G' => $objPHPExcel->getActiveSheet()->getStyle("G$sourceRow"),
                'H' => $objPHPExcel->getActiveSheet()->getStyle("H$sourceRow"),
            ];
            
            $counter = 1;
            foreach ($data['items'] as $item){
                $objPHPExcel->getActiveSheet()
                    ->mergeCells("C$i:D$i")    
                    ->setCellValue("B$i", $counter)
                    ->setCellValue("C$i", $item['goodName'])
                    ->setCellValue("E$i", $item['unit'])
                    ->setCellValue("F$i", $item['quantity'])
                    ->setCellValue("G$i", $item['price'])
                    ->setCellValue("H$i", $item['total'])
                    ->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("B$sourceRow"), "B$i")    
                    ->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("C$sourceRow"), "C$i")    
                    ->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("E$sourceRow"), "E$i")    
                    ->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("F$sourceRow"), "F$i")    
                    ->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("G$sourceRow"), "G$i")    
                    ->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle("H$sourceRow"), "H$i")    
                        ;
                
                $counter++;
                $i++;
            }
            $objPHPExcel->getActiveSheet()->removeRow($sourceRow);
            
            $objWriter = $phpExcelService->createWriter($objPHPExcel, 'Excel5' );

            $filename = tempnam($this::TMP_DIR, 'inv');
            
            if ($filename){
                $objWriter->save($filename);
            
                return $filename;    
            }    
        }
        
        return;
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
