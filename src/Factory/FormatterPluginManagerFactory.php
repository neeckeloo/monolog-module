<?php
namespace MonologModule\Factory;

use MonologModule\Formatter\FormatterPluginManager;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormatterPluginManagerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return FormatterPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $configInstance = new Config($config['monolog']['formatter_plugin_manager']);

        return new FormatterPluginManager($configInstance);
    }
}
