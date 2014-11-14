<?php
namespace MonologModule\Factory;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use MonologModule\Exception;
use ReflectionClass;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class LoggerFactory
{
    use ServiceLocatorAwareTrait;

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
        if (is_string($handler) && $this->serviceLocator->has($handler)) {
            return $this->serviceLocator->get($handler);
        }

        if (!isset($handler['name'])) {
            throw new Exception\RuntimeException('You must provide a name for each handler');
        }
        if (!class_exists($handler['name'])) {
            throw new Exception\RuntimeException(sprintf(
                'Logger handler "%s" does not exists',
                $handler['name']
            ));
        }

        $instance = $this->createInstanceFromParams($handler);

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
        if (is_string($formatter) && $this->serviceLocator->has($formatter)) {
            return $this->serviceLocator->get($formatter);
        }

        if (!isset($formatter['name'])) {
            throw new Exception\RuntimeException('You must provide a name for each formatter');
        }
        if (!class_exists($formatter['name'])) {
            throw new Exception\RuntimeException(sprintf(
                'Logger formatter "%s" does not exists',
                $formatter['name']
            ));
        }

        return $this->createInstanceFromParams($formatter);
    }

    /**
     * @param  array $params
     * @return object
     */
    private function createInstanceFromParams($params)
    {
        if (isset($params['options']) && is_array($params['options']) && !empty($params['options'])) {
            $reflection = new ReflectionClass($params['name']);

            return call_user_func_array(array($reflection, 'newInstance'), $params['options']);
        }

        $class = $params['name'];

        return new $class();
    }

    /**
     * @param  mixed $processor
     * @return Callable
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
}
