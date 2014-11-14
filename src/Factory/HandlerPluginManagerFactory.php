<?php
namespace MonologModule\Factory;

use MonologModule\Handler\HandlerPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HandlerPluginManagerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return HandlerPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configInstance = new Config($config['monolog']['handler_plugin_manager']);

        return new HandlerPluginManager($configInstance);
    }
}
