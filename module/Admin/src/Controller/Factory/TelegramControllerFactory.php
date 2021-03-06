<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\TelegramController;
use Admin\Service\TelegramManager;


/**
 * Description of TelegrammControllerFactory
 *
 * @author Daddy
 */
class TelegramControllerFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, 
                     $requestedName, array $options = null)
    {
        $telegramManager = $container->get(TelegramManager::class);
        
        // Инстанцируем контроллер и внедряем зависимости.
        return new TelegramController($telegramManager);
    }
}
