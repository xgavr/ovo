<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Application\Entity\PriceGetting;


class ProcessingController extends AbstractActionController
{
    
    /**
     * Менеджер сущностей.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * PostManager manager.
     * @var Admin\Service\PostManager
     */
    private $postManager;    
    
    /**
     * TelegramManager manager.
     * @var Admin\Service\TelegrammManager
     */
    private $telegramManager;    
    
    /**
     * PriceManager manager.
     * @var Application\Service\PriceManager
     */
    private $priceManager;    

    /**
     * RawManager manager.
     * @var Application\Service\RawManager
     */
    private $rawManager;    

    /**
     * SupplierManager manager.
     * @var Application\Service\SupplierManager
     */
    private $supplierManager;    

    /**
     * AdminManager manager.
     * @var Admin\Service\AdminManager
     */
    private $adminManager;    

    /**
     * ProducerManager manager.
     * @var Admin\Service\ProducerManager
     */
    private $producerManager;    

    
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($entityManager, $postManager, $telegramManager, 
            $priceManager, $rawManager, $supplierManager, $adminManager, $producerManager) 
    {
        $this->entityManager = $entityManager;
        $this->postManager = $postManager;        
        $this->telegramManager = $telegramManager;
        $this->priceManager = $priceManager;
        $this->rawManager = $rawManager;
        $this->supplierManager = $supplierManager;
        $this->adminManager = $adminManager;
        $this->producerManager = $producerManager;
    }   

    
    public function indexAction()
    {
        return [];
    }
    
    /*
     * Сообщения в телеграм
     * $post api_key, chat_id, text
     */
    public function telegramAction()
    {
        
        return new JsonModel(
            $data
        );
    }
    
    /*
     * Скачать все прайсы по ссылкам
     */
    public function pricesByLinkAction()
    {
        $settings = $this->adminManager->getPriceSettings();

        if ($settings['receiving_link'] == 1){
            $priceGettings = $this->entityManager->getRepository(PriceGetting::class)
                    ->findBy(['status' => PriceGetting::STATUS_ACTIVE]);

            foreach ($priceGettings as $priceGetting){
                $this->priceManager->getPriceByLink($priceGetting);
            }
        }    
        
        return new JsonModel(
            ['ok']
        );
        
    }
    
    /*
     * Чтение почтовых ящиков для прайсов
     */
    public function pricesByMailAction()
    {
        $settings = $this->adminManager->getPriceSettings();

        if ($settings['receiving_mail'] == 1){
            $priceGettings = $this->entityManager->getRepository(PriceGetting::class)
                    ->findBy(['status' => PriceGetting::STATUS_ACTIVE]);

            foreach ($priceGettings as $priceGetting){
                $this->priceManager->getPriceByMail($priceGetting);
            }
        }    
        
        return new JsonModel(
            ['ok']
        );
    }
    
    /*
     * Загрузка прайсов в базу
     */
    public function rawPricesAction()
    {
        set_time_limit(0);

        $settings = $this->adminManager->getPriceSettings();
        
        if ($settings['upload_raw'] == 1){
            
            $files = $this->supplierManager->getPriceFilesToUpload();
            if (count($files)){
                $this->rawManager->checkSupplierPrice($files[0]['priceGetting']->getSupplier());
            }            
        }    
        
        return new JsonModel(
            ['ok']
        );
    }
    
    /*
     * Удаление старых прайсов
     */
    public function deleteOldPricesAction()
    {
        set_time_limit(0);
        
        $raws = $this->entityManager->getRepository(\Application\Entity\Raw::class)
                ->findRawForRemove();
        
        foreach ($raws as $raw){
            $this->rawManager->removeRaw($raw);
        }
        
        return new JsonModel(
            ['ok']
        );
    }
    
    /*
     * Обновление количества товаров у производителя
     */
    public function producerGoodsCountAction()
    {
        set_time_limit(0);
        
        $this->producerManager->updateGoodsCounts();

        return new JsonModel(
            ['ok']
        );
    }
    
}
