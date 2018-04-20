<?php
namespace Blank\Controller\Factory;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Blank\Controller\IndexController;
use Blank\Service\BlankManager;
/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Instantiate the controller and inject dependencies
        $blankManager = $container->get(BlankManager::class);
        
        return new IndexController($blankManager);
    }
}