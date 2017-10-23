<?php

declare(strict_types=1);

namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use MonologModule\Formatter\FormatterPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormatterPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : FormatterPluginManager
    {
        $config = $container->get('Config');

        return new FormatterPluginManager(
            $container,
            $config['monolog']['formatter_plugin_manager']
        );
    }

    public function createService(ServiceLocatorInterface $serviceLocator) : FormatterPluginManager
    {
        return $this->__invoke($serviceLocator, FormatterPluginManager::class);
    }
}
