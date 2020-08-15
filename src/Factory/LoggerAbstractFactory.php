<?php

declare(strict_types=1);

namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;

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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : LoggerInterface
    {
        $config       = $container->get('Config');
        $loggerConfig = $this->getLoggerConfig($config['monolog'], $requestedName);

        $factory = $container->get(LoggerFactory::class);
        $factory->setContainer($container);

        return $factory->create($loggerConfig);
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
