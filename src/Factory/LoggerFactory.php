<?php
namespace MonologModule\Factory;

use Interop\Container\ContainerInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use MonologModule\Formatter\FormatterPluginManager;
use MonologModule\Handler\HandlerPluginManager;
use MonologModule\Exception;
use ReflectionClass;

class LoggerFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param  array $config
     * @return Logger
     */
    public function create(array $config)
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

    /**
     * @param  mixed $handler
     * @return HandlerInterface
     */
    private function createHandler($handler)
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
        $instance             = $this->createInstanceFromParams($handler, $handlerPluginManager);

        if (isset($handler['formatter'])) {
            $formatter = $this->createFormatter($handler['formatter']);
            $instance->setFormatter($formatter);
        }

        return $instance;
    }

    /**
     * @param  mixed $formatter
     * @return FormatterInterface
     */
    private function createFormatter($formatter)
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

        return $this->createInstanceFromParams($formatter, $formatterPluginManager);
    }

    /**
     * @param  array $params
     * @param  \Zend\ServiceManager\AbstractPluginManager|null $pluginManager
     * @return object
     */
    private function createInstanceFromParams($params, $pluginManager = null)
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
     * @param  string $name
     * @return \Zend\ServiceManager\AbstractPluginManager
     */
    protected function getPluginManager($name)
    {
        if (!$this->container || !$this->container->has($name)) {
            return null;
        }

        return $this->container->get($name);
    }
}
