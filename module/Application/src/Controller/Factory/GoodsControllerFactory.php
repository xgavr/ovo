<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\GoodsController;
use Application\Service\GoodsManager;
/**
 * Description of GoodsControllerFactory
 *
 * @author Daddy
 */
class GoodsControllerFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, 
                     $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $goodsManager = $container->get(GoodsManager::class);
        
        // Инстанцируем контроллер и внедряем зависимости.
        return new GoodsController($entityManager, $goodsManager);
    }
}
