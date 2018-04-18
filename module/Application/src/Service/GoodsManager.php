<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Application\Entity\Goods;
use Application\Entity\GoodsGroup;
use Application\Entity\Producer;
use Application\Entity\Tax;
use Application\Entity\Cart;
use Application\Entity\Bid;
use MvlabsPHPExcel\Service;
use Zend\Config\Config;
use Zend\Config\Writer\PhpArray;

/**
 * Description of GoodsService
 *
 * @author Daddy
 */
class GoodsManager
{
    
    const SETTINGS_DIR       = './data/settings/'; // папка с настройками
    const SETTINGS_FILE       = './data/settings/config.php'; // файл с настройками

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
  
    // Конструктор, используемый для внедрения зависимостей в сервис.
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function getSettings()
    {
        if (file_exists(self::SETTINGS_FILE)){
            $config = new Config(include self::SETTINGS_FILE);
        }  else {
            $config = new Config([], true);
            $config->good = [];
        }   
        return $config->good;
    }
    
    public function setSettings($data)
    {
        if (!is_dir(self::SETTINGS_DIR)){
            mkdir(self::SETTINGS_DIR);
        }        
        if (file_exists(self::SETTINGS_FILE)){
            $config = new Config(include self::SETTINGS_FILE, true);
        }  else {
            $config = new Config([], true);
            $config->good = [];
        }
        
        $config->good->defaultTax = $data['defaultTax'];
        
        $writer = new PhpArray();
        
        $writer->toFile(self::SETTINGS_FILE, $config);
    }
    
    public function addNewGoods($data, $flushnow=true) 
    {
        // Создаем новую сущность Goods.
        $goods = new Goods();
        $goods->setName($data['name']);
        $goods->setCode($data['code']);
        $goods->setAvailable($data['available']);
        $goods->setDescription($data['description']);
        $goods->setPrice($data['price']);
        
        $producer = $this->entityManager->getRepository(Producer::class)
                    ->findOneById($data['producer']);
        if ($producer == null){
            $producer = new Producer();
        }
        
        $goods->setProducer($producer);
        if (array_key_exists('tax', $data)){
            if (!$data['tax']) $data['tax'] = $this->getSettings()->defaultTax;
        } else {
            $data['tax'] = $this->getSettings()->defaultTax;
        }    
        
        $tax = $this->entityManager->getRepository(Tax::class)
                    ->findOneById($data['tax']);
        if ($tax == null){
            $tax = new Tax();
        }
        
        $goods->setTax($tax);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($goods);
        
        if ($flushnow){
            // Применяем изменения к базе данных.
            $this->entityManager->flush();
        }
        
        return $goods;
    }   
    
    public function updateGoods($goods, $data) 
    {
        $goods->setName($data['name']);
        $goods->setCode($data['code']);
        $goods->setAvailable($data['available']);
        $goods->setDescription($data['description']);
               
        $producer = $this->entityManager->getRepository(Producer::class)
                    ->findOneById($data['producer']);
        if ($producer == null){
            $producer = new Producer();
        }
        
        $goods->setProducer($producer);
        
        $tax = $this->entityManager->getRepository(Tax::class)
                    ->findOneById($data['tax']);
        if ($tax == null){
            $tax = new Tax();
        }
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
        
        return $goods;
    }
    
    public function removeGood($good, $flushnow = true) 
    {   
        $carts = $this->entityManager->getRepository(Cart::class)
                ->getCartInGood($good);

        if (count($carts)){
            return;
        }
                
        $bids = $this->entityManager->getRepository(Bid::class)
                ->getBidInGood($good);
        
        if (count($bids)){
            return;
        }
                
        $rawprices = $this->entityManager->getRepository(Goods::class)
                    ->findGoodRawprice($good);
        
        foreach ($rawprices as $rawprice){
            $rawprice->setGood(null);
            $this->entityManager->persist($rawprice);
        }
        
//        $images = $this->entityManager->getRepository(Goods::class)
//                    ->findGoodsImage($good);
//        
//        foreach ($images as $image){
//            $this->entityManager->remove($image);
//        }
        
        $this->entityManager->remove($good);
        
        if ($flushnow){
            $this->entityManager->flush();
        }    
    }

    public function removeAllGoods()
    {
       $goods = $this->entityManager->getRepository(Goods::class)->findAll();
       
       foreach ($goods as $good){
           $this->removeGood($good, false);
       }
       
       $this->entityManager->flush();
    }

    public function getMaxPrice($good)
    {
        $result = $this->entityManager->getRepository(Goods::class)
                ->getMaxPrice($good);
//        var_dump($result);
        if (is_array($result) && count($result)){
            if (array_key_exists('price', $result[0])){    
                return $result[0]['price'];
            }
        }    
        return 0;
    } 
    
    public function addNewGroup($data)
    {
        $group = new GoodsGroup();
        $group->setName($data['name']);
        $group->setDescription($data['description']);
                
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($group);
        
        $this->entityManager->flush();
        
        return $group;        
    }
    
    public function updateGroup($group, $data) 
    {
        $group->setName($data['name']);
        $group->setDescription($data['description']);
               
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
        
        return $group;
    }
    
    public function removeGroup($group)
    {
        
        foreach ($group->getGoods() as $good){
            $this->removeGood($good, false);
        }
                
        $this->entityManager->remove($group);
        
        $this->entityManager->flush();
        
    }
    
}
