<?php
namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use MonologModule\Handler\HandlerPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HandlerPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        $handlerPluginManager = new HandlerPluginManager(
            $container,
            $config['monolog']['handler_plugin_manager']
        );
        $handlerPluginManager->setServiceLocator($container);
        return $handlerPluginManager;
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, HandlerPluginManager::class);
    }
}
