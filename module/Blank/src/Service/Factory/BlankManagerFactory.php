<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Blank\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Blank\Service\BlankManager;
/**
 * Description of BlankManagerFactory
 *
 * @author Daddy
 */
class BlankManagerFactory  implements FactoryInterface
{
                   
    public function __invoke(ContainerInterface $container, 
                    $requestedName, array $options = null)
    {
        
        // Инстанцируем сервис и внедряем зависимости.
        return new BlankManager();
    }
}
