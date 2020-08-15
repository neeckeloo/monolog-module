<?php

declare(strict_types=1);

namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use MonologModule\Handler\HandlerPluginManager;

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
}
