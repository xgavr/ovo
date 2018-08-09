<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Service\PostManager;
use Zend\Mail\Transport\SmtpOptions;

/**
 * Description of ShopManagerFactory
 *
 * @author Daddy
 */
class PostManagerFactory  implements FactoryInterface
{
                   
    public function __invoke(ContainerInterface $container, 
                    $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $config = $container->get('config');
        $smtpTransportOptions = new SmtpOptions($config['mail']['transport']['options']);
        
        // Инстанцируем сервис и внедряем зависимости.
        return new PostManager($entityManager, $smtpTransportOptions);
    }
}
