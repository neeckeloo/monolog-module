<?php

declare(strict_types=1);

namespace MonologModule\Handler\Factory;

use Gelf;
use Interop\Container\ContainerInterface;
use Monolog\Handler\GelfHandler;
use Monolog\Logger;
use MonologModule\Exception;
use Laminas\ServiceManager\Factory\FactoryInterface;

class GelfHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : GelfHandler
    {
        if (!isset($options['host'])) {
            throw new Exception\RuntimeException('Gelf handler needs a host value');
        }
        if (!isset($options['port'])) {
            throw new Exception\RuntimeException('Gelf handler needs a port value');
        }

        $publisher = new Gelf\Publisher(
            new Gelf\Transport\UdpTransport($options['host'], $options['port'])
        );

        if (isset($options['level'])) {
            $level = $options['level'];
        } else {
            $level = Logger::DEBUG;
        }

        if (isset($options['bubble'])) {
            $bubble = $options['bubble'];
        } else {
            $bubble = true;
        }

        return new GelfHandler($publisher, $level, $bubble);
    }
}
