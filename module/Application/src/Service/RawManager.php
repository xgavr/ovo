<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Application\Entity\Supplier;
use Application\Entity\Tax;
use Application\Entity\Country;
use Application\Entity\Producer;
use Application\Entity\UnknownProducer;
use Application\Entity\Raw;
use Application\Entity\Rawprice;
use Application\Entity\Goods;
use MvlabsPHPExcel\Service;
use Zend\Json\Json;

/**
 * Description of PriceManager
 *
 * @author Daddy
 */
class RawManager {
    
    const PRICE_FOLDER       = './data/prices'; // папка с прайсами
    const PRICE_FOLDER_ARX   = './data/prices/arx'; // папка с архивами прайсов
    
    static $storedRawData = null;

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
  
    private $producerManager;
  
    private $goodManager;
    
  // Конструктор, используемый для внедрения зависимостей в сервис.
    public function __construct($entityManager, $producerManager, $goodManager)
    {
        $this->entityManager = $entityManager;
        $this->producerManager = $producerManager;
        $this->goodManager = $goodManager;
    }
    
    public function getPriceFolder()
    {
        return self::PRICE_FOLDER;
    }        

    public function getPriceArxFolder()
    {
        return self::PRICE_FOLDER_ARX;
    }      
    
    
    /*
     * Очистить содержимое папки
     * 
     * @var Application\Entity\Supplier $supplier
     * @var string $folderName
     * 
     */
    public function clearPriceFolder($supplier, $folderName)
    {
        if (is_dir($folderName)){
            if ($dh = opendir($folderName)) {
                while (($file = readdir($dh)) !== false) {
                    if($file != "." && $file != ".."){ // если это не папка
                        if(is_file($folderName."/".$file)){ // если файл
                            unlink($folderName."/".$file);                            
                        }                        
                        // если папка, то рекурсивно вызываем
                        if(is_dir($folderName."/".$file)){
                            $this->clearPriceFolder($supplier, $folderName."/".$file);
                        }
                    }           
                }
                closedir($dh);
                
                if ($folderName != self::PRICE_FOLDER.'/'.$supplier->getId()){
                    rmdir($folderName);
                }
            }
        }
        
    }
    
    /**
     * Загрузка сырого прайса
     * @var Application\Entity\Supplier
     * @var string $filename
     */
    
    public function uploadRawprice($supplier, $filename)
    {
        ini_set('memory_limit', '512M');
        
        if (file_exists($filename)){
            $mvexcel = new Service\PhpExcelService(); 
            
            try{
                try {
                    $excel = $mvexcel->createPHPExcelObject($filename);
                } catch (Exception $e) {
                    var_dump($e->getMessage()); return;
                }    
            } catch (Exception $e){
                var_dump($e->getMessage()); return;                
            }    
            
            $raw = new Raw();
            $raw->setSupplier($supplier);
            $raw->setFilename($filename);
            $raw->setStatus($raw->getStatusActive());

            $currentDate = date('Y-m-d H:i:s');
            $raw->setDateCreated($currentDate);

            $sheets = $excel->getAllSheets();
            foreach ($sheets as $sheet) { // PHPExcel_Worksheet

                $excel_sheet_content = $sheet->toArray();

                if (count($sheet)){
                    foreach ($excel_sheet_content as $row){
                        $rawprice = new Rawprice();

                        $rawprice->setRawdata(Json::encode($row));

                        $rawprice->setArticle('');
                        $rawprice->setGoodname('');
                        $rawprice->setDescription('');
                        $rawprice->setImage('');
                        $rawprice->setProducer('');
                        $rawprice->setCountry('');
                        $rawprice->setPrice(0);
                        $rawprice->setCurrency('');
                        $rawprice->setRate(0);
                        $rawprice->setRest(0);
                        $rawprice->setUnit('');

                        $rawprice->setRaw($raw);

//                        $unknownProducer = new UnknownProducer();
//                        $rawprice->setUnknownProducer($unknownProducer);

//                        $good = new Goods();
//                        $rawprice->setGood($good);

                        $currentDate = date('Y-m-d H:i:s');
                        $rawprice->setDateCreated($currentDate);

                        // Добавляем сущность в менеджер сущностей.
                        $this->entityManager->persist($rawprice);

                        $raw->addRawprice($rawprice);

                    }
                    // Применяем изменения к базе данных.
                }	
            }

            $this->entityManager->persist($raw);

            $this->entityManager->flush();                    

            unset($excel);
            unset($mvexcel);
        }
    }
    
    /*
     * 
     * Проверка папки с прайсами. Если в папке есть прайс то загружаем его
     * 
     * @var Application\Entity\Supplier $supplier
     * @var string $folderName
     * 
     */
    public function checkPriceFolder($supplier, $folderName)
    {
    
        if (is_dir($folderName)){
            if ($dh = opendir($folderName)) {
                while (($file = readdir($dh)) !== false) {
                    if($file != "." && $file != ".."){ // если это не папка
                        if(is_file($folderName."/".$file)){ // если файл
                            
                            if ($supplier->getStatus() == $supplier->getStatusActive()){
                                $this->uploadRawprice($supplier, $folderName."/".$file);
                            }
                            
                            $arx_folder = self::PRICE_FOLDER_ARX.'/'.$supplier->getId();
                            if (is_dir($arx_folder)){
                                if (!rename($folderName."/".$file, $arx_folder."/".$file)){
                                    unlink($folderName."/".$file);
                                }
                            }
                            
                        }                        
                        // если папка, то рекурсивно вызываем
                        if(is_dir($folderName."/".$file)){
                            $this->checkPriceFolder($supplier, $folderName."/".$file);
                        }
                    }           
                }
                closedir($dh);
            }
        }
    }
    
    /*
     * Проход по всем поставщикам - поиск файлов с прайсам в папках
     */
    public function checkSupplierPrice()
    {
        
        $suppliers = $this->entityManager->getRepository(Supplier::class)->findAll();
        
        foreach ($suppliers as $supplier){
            $this->checkPriceFolder($supplier, self::PRICE_FOLDER.'/'.$supplier->getId());
            $this->clearPriceFolder($supplier, self::PRICE_FOLDER.'/'.$supplier->getId());
        }
    }
    
    /*
     * Обработка данных прайса
     * @var Application\Entity\Rawprice @rawprice
     * @var Apllication\Entity\Pricesettings #paicesettings
     */
    public function parseRawdata($rawprice, $pricesetting)
    {
        $rawdata = Json::decode($rawprice->getRawdata());
        $result = [
            'article' => '',
            'producer' => '',
            'country' => '',
            'goodname' => '',
            'description' => '',
            'image' => '',
            'price' => 0,
            'currency' => '',
            'rate' => 0,
            'rest' => 0,
            'unit' => '',
        ];
        
        if ($pricesetting->getArticle() && count($rawdata) >= $pricesetting->getArticle()){
            $result['article'] = $rawdata[$pricesetting->getArticle() - 1];
        }    
        
        if ($pricesetting->getProducer() && count($rawdata) >= $pricesetting->getProducer()){
            $result['producer'] = $rawdata[$pricesetting->getProducer() - 1];
        }    
        if (!$pricesetting->getProducer()){
            $result['producer'] = $pricesetting->getSupplier()->getName(); // производитель является поставщиком
        }    
        
        if ($pricesetting->getCountry() && count($rawdata) >= $pricesetting->getCountry()){
            $result['country'] = $rawdata[$pricesetting->getCountry() - 1];
        }    
        if ($pricesetting->getTitle() && count($rawdata) >= $pricesetting->getTitle()){
            $result['goodname'] = $rawdata[$pricesetting->getTitle() - 1];
        }    
        if ($pricesetting->getDescription() && count($rawdata) >= $pricesetting->getDescription()){
            $result['description'] = $rawdata[$pricesetting->getDescription() - 1];
        }    
        if ($pricesetting->getImage() && count($rawdata) >= $pricesetting->getImage()){
            $result['image'] = $rawdata[$pricesetting->getImage() - 1];
        }    
        if ($pricesetting->getPrice() && count($rawdata) >= $pricesetting->getPrice()){
            $result['price'] = $rawdata[$pricesetting->getPrice() - 1];
        }   
        if ($pricesetting->getCurrency() && count($rawdata) >= $pricesetting->getCurrency()){
            $result['currency'] = $rawdata[$pricesetting->getCurrency() - 1];
        }   
        if ($pricesetting->getRate() && count($rawdata) >= $pricesetting->getRate()){
            $result['rate'] = $rawdata[$pricesetting->getRate() - 1];
        }   
        if ($pricesetting->getRest() && count($rawdata) >= $pricesetting->getRest()){
            $result['rest'] = $rawdata[$pricesetting->getRest() - 1];
        }    
        if ($pricesetting->getUnit() && count($rawdata) >= $pricesetting->getUnit()){
            $result['unit'] = $rawdata[$pricesetting->getUnit() - 1];
        }    
        
//        if ($result['producer'] && $result['goodname'] && $result['price']){        
            return $result;
//        }    
        
        return;
    }
    
    /*
     * @var array @parsedates
     */
    
    protected function selectBestParsedata($parsedates)
    {
        if (count($parsedates == 1)){
            return $parsedates[0];
        }
        
        foreach ($parsedates as $parsedata){
            /*Какие то правила выбора лучшего набора данных*/
            return $parsedata;
        }
        
        return;
    }
    
    /*
     * @var Application\Entity\Rawprice $rawprice
     * @var array @parsedata
     * @var bool $flushnow
     */
    
    protected function updateParsedata($rawprice, $parsedata, $flushnow, $storeData = false)
    {
//        var_dump($storeData);
        if ($storeData && is_array($this::$storedRawData)){
            if (!$parsedata['article'] && $this::$storedRawData['article']) $parsedata['article'] = $this::$storedRawData['article'];
            if (!$parsedata['producer'] && $this::$storedRawData['producer']) $parsedata['producer'] = $this::$storedRawData['producer'];
            if (!$parsedata['country'] && $this::$storedRawData['country']) $parsedata['country'] = $this::$storedRawData['country'];
            if (!$parsedata['goodname'] && $this::$storedRawData['goodname']) $parsedata['goodname'] = $this::$storedRawData['goodname'];
            if (!$parsedata['description'] && $this::$storedRawData['description']) $parsedata['description'] = $this::$storedRawData['description'];
            if (!$parsedata['image'] && $this::$storedRawData['image']) $parsedata['image'] = $this::$storedRawData['image'];
            if (!$parsedata['currency'] && $this::$storedRawData['currency']) $parsedata['currency'] = $this::$storedRawData['currency'];
            if (!$parsedata['rate'] && $this::$storedRawData['rate']) $parsedata['rate'] = $this::$storedRawData['rate'];
        }
        
        $rawprice->setArticle($parsedata['article']);
        $rawprice->setProducer($parsedata['producer']);
        $rawprice->setCountry($parsedata['country']);
        $rawprice->setGoodname($parsedata['goodname']);
        $rawprice->setDescription($parsedata['description']);
        $rawprice->setImage($parsedata['image']);
        $rawprice->setCurrency($parsedata['currency']);
        $rawprice->setRate($parsedata['rate']);

        $rawprice->setPrice($parsedata['price']);
        $rawprice->setRest($parsedata['rest']);
        $rawprice->setUnit($parsedata['unit']);
        
        $this->entityManager->persist($rawprice);
        
        if ($flushnow){
            $this->entityManager->flush();
        }    
        
        if ($storeData){
            $this::$storedRawData = $parsedata;
        }
    }
    
    /*
     * Обработка строки rawprice
     * @var Application\Entity\Rawprice $rawprice;
     * @bool $flushnow
     */
    public function parseRawprice($rawprice, $flushnow = true, $storeData = false)
    {
        ini_set('memory_limit', '512M');
        
        $raw = $rawprice->getRaw();
        $pricesettings = $raw->getSupplier()->getPricesettings();
        
        $data = [];
        foreach ($pricesettings as $pricesetting){
            if ($pricesetting->getStatus() == $pricesetting->getStatusActive()){
                $parceData = $this->parseRawdata($rawprice,$pricesetting);
                if (is_array($parceData)){
                    $data[] = $parceData; 
                }            
            }            
        }
        
        if (count($data)){
            $this->updateParsedata($rawprice, $this->selectBestParsedata($data), $flushnow, $storeData);
        }    
        
        return;
    }
    
    /*
     * Парсить все записи
     * @var Application\Entity\Raw @raw
     * 
     */
    public function parseRaw($raw)
    {
        $this::$storedRawData = null;
        
        foreach ($raw->getRawprice() as $rawprice){
            $this->parseRawprice($rawprice, false, true);
        }
        
        $this->entityManager->flush();
    }
    
    /*
     * Собрать неизвестных поставщиков
     * @var Application\Entity\Rawprice
     * 
     */
    public function unknownProducerRawprice($rawprice, $flushnow = true)
    {
        if ($rawprice->getProducer()){
            $unknownProducer = $this->producerManager->addUnknownProducer($rawprice->getProducer(), false);
            $rawprice->setUnknownProducer($unknownProducer);
            $this->entityManager->persist($rawprice);        
        }
        if ($flushnow){        
            $this->entityManager->flush();
        }    
    }

    /*
     * Выбрать и добавить уникальных производителей
     * @var Application\Entity\Raw @raw
     * 
     */    
    public function addNewUnknownProducerRaw($raw)
    {
        $producers = $this->entityManager->getRepository(Raw::class)
                ->findProducerRawprice($raw);
        foreach ($producers as $producer){
            if (is_string($producer['producer']) && $producer['producer']){
                $this->producerManager->addUnknownProducer($producer['producer'], false);
            }    
        }
        $this->entityManager->flush();
    }
    
    /*
     * Парсить все записи
     * @var Application\Entity\Raw @raw
     * 
     */
    public function unknownProducerRaw($raw)
    {
        foreach ($raw->getRawprice() as $rawprice){
            $this->unknownProducerRawprice($rawprice, false);
        }
        
        $this->entityManager->flush();
    }
    
    public function getGoodName($rawprice)
    {
        if (is_array($rawprice)){
            $result = $rawprice['goodname'];
            if ($rawprice['unit']){
                $result .= ' '.$rawprice['unit'];
            }
            if (!$rawprice['article'] && !$rawprice['unit']){
                $result .= ' '.$rawprice['description'];
            }
        } else {
            $result = $rawprice->getGoodname();
            if ($rawprice->getUnit()){
                $result .= ' '.$rawprice->getUnit();
            }            
            if (!$rawprice->getArticle() && !$rawprice->getUnit()){
                $result .= ' '.$rawprice->getDescription();
            }
        }
        
        return trim($result);
    }
    
    /*
     * Выбрать и добавить уникальные товары
     * @var Application\Entity\Raw @raw
     * 
     */    
    public function addNewGoodsRaw($raw)
    {
        ini_set('memory_limit', '512M');
        
        $rawprices = $this->entityManager->getRepository(Raw::class)
                ->findGoodRawprice($raw);

        foreach ($rawprices as $rawprice){

            if ($rawprice['goodname'] && $rawprice['unknownProducer']){
                
                $unknownProducer = $this->entityManager->getRepository(UnknownProducer::class)
                        ->findOneById($rawprice['unknownProducer']);
                
                if ($unknownProducer && $unknownProducer->getProducer()){
                    
                    $good = $this->entityManager->getRepository(Goods::class)
                                ->findOneBy([
                                    'producer' => $unknownProducer->getProducer(), 
                                    'code' => $rawprice['article'],
                                    'name' => $this->getGoodName($rawprice),
                                ]);
                    
                    if ($good == NULL){
                        $good = $this->goodManager->addNewGoods([
                            'name' => $this->getGoodName($rawprice),
                            'code' => $rawprice['article'],
                            'available' => Goods::AVAILABLE_TRUE,
                            'description' => $rawprice['description'],
                            'producer' => $unknownProducer->getProducer(),
                            'price' => 0,                        
                        ], false);
                    }                
                }    
            }    
        }
        $this->entityManager->flush();
    }
    
    
    /*
     * Привязать товар к прайсу
     * @var Application\Entity\Rawprice
     */
    public function addGoodRawprice($rawprice, $flushnow = true)
    {
        if ($rawprice->getUnknownProducer()){
            if ($rawprice->getUnknownProducer()->getProducer() && $rawprice->getGoodname()){
                $good = $this->entityManager->getRepository(Goods::class)
                            ->findOneBy([
                                'producer' => $rawprice->getUnknownProducer()->getProducer()->getId(), 
                                'code' => $rawprice->getArticle(),
                                'name' => $this->getGoodName($rawprice),
                            ]);
                if ($good == NULL){                    
                    $good = $this->goodManager->addNewGoods([
                        'name' => $this->getGoodName($rawprice),
                        'code' =>$rawprice->getArticle(),
                        'available' => Goods::AVAILABLE_TRUE,
                        'description' => $rawprice->getDescription(),
                        'producer' => $rawprice->getUnknownProducer()->getProducer(),
                        'price' => $rawprice->getPrice(),
                    ]);
                    
//                } else {
//                    $this->goodManager->updateGoods($good, [
//                        'name' => $this->getGoodName($rawprice),
//                        'code' =>$rawprice->getArticle(),
//                        'available' => Goods::AVAILABLE_TRUE,
//                        'description' => $rawprice->getDescription(),
//                        'producer' => $rawprice->getUnknownProducer()->getProducer(),
//                    ]);                    
                }
                
                $rawprice->setGood($good);
                
                $this->entityManager->persist($rawprice);        
                if ($flushnow){
                    $this->entityManager->flush();    
                }
            }
        }
    }
    
    public function updateGoodRawprice($rawprice, $flushnow = true)
    {
        if ($rawprice->getUnknownProducer() && $rawprice->getGood()){
            if ($rawprice->getUnknownProducer()->getProducer() && $rawprice->getGoodname()){
                $good = $this->entityManager->getRepository(Goods::class)
                            ->findOneBy([
                                'producer' => $rawprice->getUnknownProducer()->getProducer(), 
                                'code' => $rawprice->getArticle(),
                                'name' => $this->getGoodName($rawprice),
                            ]);
                if ($good == NULL){
                    $good = $this->goodManager->addNewGoods([
                        'name' => $this->getGoodName($rawprice),
                        'code' =>$rawprice->getArticle(),
                        'available' => Goods::AVAILABLE_TRUE,
                        'description' => $rawprice->getDescription(),
                        'producer' => $rawprice->getUnknownProducer()->getProducer(),
                        'price' => $rawprice->getPrice(),
                    ]);
                    $rawprice->setGood($good);
                } else {
                    $this->goodManager->updateGoods($good, [
                        'name' => $this->getGoodName($rawprice),
                        'code' =>$rawprice->getArticle(),
                        'available' => Goods::AVAILABLE_TRUE,
                        'description' => $rawprice->getDescription(),
                        'producer' => $rawprice->getUnknownProducer()->getProducer(),
                    ]);                    
                    
                }
                
                $rawprice->setGood($good);
                $this->entityManager->persist($rawprice);        
                if ($flushnow){
                    $this->entityManager->flush();    
                }
            }
        }
    }
    
    /*
     * Установить цену товара
     * @var Application\Entity\Rawprice $rawprice
     */
    public function setPriceRawprice($rawprice, $flushnow = true)
    {
        if ($rawprice->getGood()){
            
            $good = $rawprice->getGood();
            $price = $this->goodManager->getMaxPrice($good);
            
            $good->setPrice($price);
            $this->entityManager->persist($good);        
            if ($flushnow){
                $this->entityManager->flush();    
            }
        }        
    }


    /*
     * Парсить все записи
     * @var Application\Entity\Raw @raw
     * 
     */
    public function addGoodRaw($raw)
    {
        set_time_limit(180);
        ini_set('memory_limit', '512M');
        
        foreach ($raw->getRawprice() as $rawprice){
            $this->addGoodRawprice($rawprice, false);
        }
        
        $this->entityManager->flush();
    }
    
    /*
     * Парсить все записи
     * @var Application\Entity\Raw @raw
     * 
     */
    public function updateGoodRaw($raw)
    {
        foreach ($raw->getRawprice() as $rawprice){
            $this->updateGoodRawprice($rawprice, false);
        }
        
        $this->entityManager->flush();
    }
    
    /*
     * Установить цену в товарах прайса
     * @var Application\Entity\Raw @raw
     * 
     */
    public function setPriceRaw($raw)
    {
        set_time_limit(180);
        ini_set('memory_limit', '512M');

        foreach ($raw->getRawprice() as $rawprice){
            $this->setPriceRawprice($rawprice, false);
        }
        
        $this->entityManager->flush();
    }
        
    public function removeRawprice($rawprice)
    {
        $this->entityManager->remove($rawprice);
        $this->entityManager->flush();        
    }
    
    public function removeRaw($raw)
    {
        $rawprices = $raw->getRawprice();
        foreach ($rawprices as $rawprice){
            $this->entityManager->remove($rawprice);
        }        
        
        $this->entityManager->remove($raw);
        $this->entityManager->flush();
    }
        
}
