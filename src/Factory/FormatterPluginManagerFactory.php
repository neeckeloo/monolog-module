<?php
namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use MonologModule\Formatter\FormatterPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormatterPluginManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        
        $formatterPluginManager = new FormatterPluginManager(
            $container,
            $config['monolog']['formatter_plugin_manager']
        );
        if (class_exists('Zend\Version\Version') && \Zend\Version\Version::compareVersion('3.0') >=1) {
            $formatterPluginManager->setServiceLocator($container);
        }
        return $formatterPluginManager;
    }
    
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, FormatterPluginManager::class);
    }
}
