<?php
namespace MonologModuleTest\Handler\Factory;

use MonologModule\Handler\Factory\GelfHandlerFactory;
use PHPUnit_Framework_TestCase;

class GelfHandlerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateGelfHandler()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $gelfHandlerFactory = new GelfHandlerFactory([
            'host' => 'domain.com',
            'port' => 123,
        ]);
        $gelfHandler = $gelfHandlerFactory->createService($serviceLocator);

        $this->assertInstanceOf('Monolog\Handler\GelfHandler', $gelfHandler);
    }

    /**
     * @expectedException MonologModule\Exception\RuntimeException
     */
    public function testInstantiateGelfHandlerWithoutHost()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $gelfHandlerFactory = new GelfHandlerFactory(['port' => 123]);
        $gelfHandlerFactory->createService($serviceLocator);
    }

    /**
     * @expectedException MonologModule\Exception\RuntimeException
     */
    public function testInstantiateGelfHandlerWithoutPort()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $gelfHandlerFactory = new GelfHandlerFactory(['host' => 'domain.com']);
        $gelfHandlerFactory->createService($serviceLocator);
    }
}
