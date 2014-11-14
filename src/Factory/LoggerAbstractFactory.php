<?php
namespace MonologModule\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config       = $serviceLocator->get('Config');
        $loggerConfig = $this->getLoggerConfig($config, $requestedName);

        return !empty($loggerConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config       = $serviceLocator->get('Config');
        $loggerConfig = $this->getLoggerConfig($config, $requestedName);

        $factory = $serviceLocator->get('MonologModule\Factory\LoggerFactory');

        return $factory->create($loggerConfig);
    }

    /**
     * @param  array $config
     * @param  string $requestedName
     * @return array
     */
    private function getLoggerConfig(array $config, $requestedName)
    {
        if (!isset($config['loggers']) || !is_array($config['loggers'])) {
            return [];
        }

        $loggers = $config['loggers'];

        if (!isset($loggers[$requestedName]) || !is_array($loggers[$requestedName])) {
            return [];
        }

        return $loggers[$requestedName];
    }
}
