<?php
namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerAbstractFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName) : bool
    {
        $config       = $container->get('Config');
        $loggerConfig = $this->getLoggerConfig($config['monolog'], $requestedName);

        return !empty($loggerConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName) : bool
    {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : LoggerInterface
    {
        $config       = $container->get('Config');
        $loggerConfig = $this->getLoggerConfig($config['monolog'], $requestedName);

        $factory = $container->get(LoggerFactory::class);
        $factory->setContainer($container);

        return $factory->create($loggerConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName) : LoggerInterface
    {
        return $this->__invoke($serviceLocator, $requestedName);
    }

    private function getLoggerConfig(array $config, string $requestedName) : array
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
