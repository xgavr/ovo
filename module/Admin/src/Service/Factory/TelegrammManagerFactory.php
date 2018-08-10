<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Service\TelegrammManager;

/**
 * Description of ShopManagerFactory
 *
 * @author Daddy
 */
class TelegrammManagerFactory  implements FactoryInterface
{
                   
    public function __invoke(ContainerInterface $container, 
                    $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $config = $container->get('config');
        $telegramOptions = $config['telegram'];

        
        // Инстанцируем сервис и внедряем зависимости.
        return new TelegrammManager($entityManager, $adminManager, $telegramOptions);
    }
}
