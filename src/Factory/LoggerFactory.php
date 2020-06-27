<?php

declare(strict_types=1);

namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use MonologModule\Formatter\FormatterPluginManager;
use MonologModule\Handler\HandlerPluginManager;
use MonologModule\Exception;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Zend\ServiceManager\AbstractPluginManager;

class LoggerFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container) : void
    {
        $this->container = $container;
    }

    public function create(array $config) : LoggerInterface
    {
        if (!isset($config['name'])) {
            throw new Exception\RuntimeException('You must provide a name for each logger');
        }

        $logger = new Logger($config['name']);

        if (isset($config['handlers']) && is_array($config['handlers'])) {
            foreach ($config['handlers'] as $handler) {
                $logger->pushHandler($this->createHandler($handler));
            }
        }
        if (isset($config['processors']) && is_array($config['processors'])) {
            foreach ($config['processors'] as $processor) {
                $logger->pushProcessor($this->createProcessor($processor));
            }
        }

        return $logger;
    }

    private function createHandler(array $handler) : HandlerInterface
    {
        if (!isset($handler['name'])) {
            throw new Exception\RuntimeException('You must provide a name for each handler');
        }
        if (!class_exists($handler['name'])) {
            throw new Exception\RuntimeException(sprintf(
                'Logger handler "%s" does not exists',
                $handler['name']
            ));
        }

        $handlerPluginManager = $this->getPluginManager(HandlerPluginManager::class);
        /* @var $instance HandlerInterface */
        $instance = $this->createInstanceFromParams($handler, $handlerPluginManager);

        if (isset($handler['formatter'])) {
            $formatter = $this->createFormatter($handler['formatter']);
            $instance->setFormatter($formatter);
        }

        return $instance;
    }

    private function createFormatter(array $formatter) : FormatterInterface
    {
        if (!isset($formatter['name'])) {
            throw new Exception\RuntimeException('You must provide a name for each formatter');
        }
        if (!class_exists($formatter['name'])) {
            throw new Exception\RuntimeException(sprintf(
                'Logger formatter "%s" does not exists',
                $formatter['name']
            ));
        }

        $formatterPluginManager = $this->getPluginManager(FormatterPluginManager::class);

        /* @var $instance FormatterInterface */
        $instance = $this->createInstanceFromParams($formatter, $formatterPluginManager);

        return $instance;
    }

    /**
     * @param  array $params
     * @param  AbstractPluginManager|null $pluginManager
     * @return object
     */
    private function createInstanceFromParams(array $params, AbstractPluginManager $pluginManager = null)
    {
        $options = [];
        if (isset($params['options']) && is_array($params['options'])) {
            $options = $params['options'];
        }

        if ($pluginManager && $pluginManager->has($params['name'])) {
            return $pluginManager->get($params['name'], $options);
        }

        if (!empty($options)) {
            $reflection = new ReflectionClass($params['name']);

            return call_user_func_array(array($reflection, 'newInstance'), $options);
        }

        $class = $params['name'];

        return new $class();
    }

    /**
     * @param  mixed $processor
     * @return callable
     */
    private function createProcessor($processor)
    {
        if (is_callable($processor)) {
            return $processor;
        }

        if (is_string($processor) && class_exists($processor)) {
            return new $processor();
        }

        throw new Exception\RuntimeException(
            'Processor must be a callable or the FQCN of an invokable class'
        );
    }

    /**
     * @param string $name
     * @return AbstractPluginManager
     */
    private function getPluginManager(string $name)
    {
        if ($this->container && $this->container->has($name)) {
            return $this->container->get($name);
        }
    }
}
