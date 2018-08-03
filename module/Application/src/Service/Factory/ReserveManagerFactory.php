<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Service\ReserveManager;
use Blank\Service\BlankManager;
use Admin\Service\PostManager;

/**
 * Description of OrderManagerFactory
 *
 * @author Daddy
 */
class ReserveManagerFactory  implements FactoryInterface
{
                   
    public function __invoke(ContainerInterface $container, 
                    $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);        
        $blankManager = $container->get(BlankManager::class);
        $postManager = $container->get(PostManager::class);
        
        // Инстанцируем сервис и внедряем зависимости.
        return new ReserveManager($entityManager, $authService, $blankManager, $postManager);
    }
}
