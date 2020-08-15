<?php

declare(strict_types=1);

namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use MonologModule\Formatter\FormatterPluginManager;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
}
