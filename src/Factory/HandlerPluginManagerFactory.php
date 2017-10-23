<?php

declare(strict_types=1);

namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use MonologModule\Handler\HandlerPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HandlerPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : HandlerPluginManager
    {
        $config = $container->get('Config');

        return new HandlerPluginManager(
            $container,
            $config['monolog']['handler_plugin_manager']
        );
    }

    public function createService(ServiceLocatorInterface $serviceLocator) : HandlerPluginManager
    {
        return $this->__invoke($serviceLocator, HandlerPluginManager::class);
    }
}
