<?php

declare(strict_types=1);

namespace MonologModule\Handler\Factory;

use Gelf;
use Interop\Container\ContainerInterface;
use Monolog\Handler\GelfHandler;
use Monolog\Logger;
use MonologModule\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GelfHandlerFactory implements FactoryInterface
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options = [])
    {
        // Zend ServiceManager v2 allows factory creationOptions
        $this->options = $options;
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : GelfHandler
    {
        /**
         * Avoid a BC break; Zend ServiceManager v2 will pass the options via the constructor, v3 to the __invoke()
         */
        if (null !== $options) {
            $this->options = array_merge($this->options, $options);
        }

        if (!isset($this->options['host'])) {
            throw new Exception\RuntimeException('Gelf handler needs a host value');
        }
        if (!isset($this->options['port'])) {
            throw new Exception\RuntimeException('Gelf handler needs a port value');
        }

        $publisher = new Gelf\Publisher(
            new Gelf\Transport\UdpTransport($this->options['host'], $this->options['port'])
        );

        if (isset($this->options['level'])) {
            $level = $this->options['level'];
        } else {
            $level = Logger::DEBUG;
        }

        if (isset($this->options['bubble'])) {
            $bubble = $this->options['bubble'];
        } else {
            $bubble = true;
        }

        return new GelfHandler($publisher, $level, $bubble);
    }

    public function createService(ServiceLocatorInterface $serviceLocator) : GelfHandler
    {
        return $this->__invoke($serviceLocator, GelfHandler::class);
    }
}
