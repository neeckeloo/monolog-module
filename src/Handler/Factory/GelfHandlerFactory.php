<?php
namespace MonologModule\Handler\Factory;

use Gelf;
use Monolog\Handler\GelfHandler;
use MonologModule\Exception;
use ReflectionClass;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GelfHandlerFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param  ServiceLocatorInterface $serviceLocator
     * @return GelfHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!isset($this->options['host'])) {
            throw new Exception\RuntimeException('Gelf handler needs a host value');
        }
        if (!isset($this->options['port'])) {
            throw new Exception\RuntimeException('Gelf handler needs a port value');
        }

        $publisher = new Gelf\Publisher(
            new Gelf\Transport\UdpTransport($this->options['host'], $this->options['port'])
        );

        $params = ['publisher' => $publisher];

        if (isset($this->options['level'])) {
            $params['level'] = $this->options['level'];
        }
        if (isset($this->options['bubble'])) {
            $params['bubble'] = $this->options['bubble'];
        }

        $reflection = new ReflectionClass('Monolog\Handler\GelfHandler');

        return call_user_func_array(array($reflection, 'newInstance'), $params);
    }
}
