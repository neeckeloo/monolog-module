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
        if (class_exists('Zend\Version\Version') && \Zend\Version\Version::compareVersion('3.0') >=1) {
            $handlerPluginManager->setServiceLocator($container);
        }
        return $handlerPluginManager;
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, HandlerPluginManager::class);
    }
}
