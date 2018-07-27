<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\ProcessingController;
use Admin\Service\PostManager;
use Admin\Service\TelegrammManager;
use Application\Service\PriceManager;
use Application\Service\RawManager;
use Application\Service\SupplierManager;
use Admin\Service\AdminManager;
use Application\Service\ProducerManager;


/**
 * Description of ClientControllerFactory
 *
 * @author Daddy
 */
class ProcessingControllerFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, 
                     $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(PostManager::class);
        $telegramManager = $container->get(TelegrammManager::class);
        $priceManager = $container->get(PriceManager::class);
        $rawManager = $container->get(RawManager::class);
        $supplierManager = $container->get(SupplierManager::class);
        $adminManager = $container->get(AdminManager::class);
        $producerManager = $container->get(ProducerManager::class);
        
        // Инстанцируем контроллер и внедряем зависимости.
        return new ProcessingController($entityManager, $postManager, $telegramManager, 
                $priceManager, $rawManager, $supplierManager, 
                $adminManager, $producerManager);
    }
}
