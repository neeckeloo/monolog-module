<?php
namespace MonologModuleTest\Handler\Factory;

use MonologModule\Handler\Factory\GelfHandlerFactory;
use Monolog\Handler\GelfHandler;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class GelfHandlerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiateGelfHandler()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $gelfHandlerFactory = new GelfHandlerFactory([
            'host' => 'domain.com',
            'port' => 123,
        ]);
        $gelfHandler = $gelfHandlerFactory->createService($serviceLocator);

        $this->assertInstanceOf(GelfHandler::class, $gelfHandler);
    }

    public function testInstantiateGelfHandlerViaInvokeOptions()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $gelfHandlerFactory = new GelfHandlerFactory();
        $gelfHandler = $gelfHandlerFactory->__invoke(
            $serviceLocator,
            GelfHandler::class,
            [
                'host' => 'domain.com',
                'port' => 123,
            ]
        );

        $this->assertInstanceOf(GelfHandler::class, $gelfHandler);
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testInstantiateGelfHandlerWithoutHost()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $gelfHandlerFactory = new GelfHandlerFactory(['port' => 123]);
        $gelfHandlerFactory->createService($serviceLocator);
    }

    /**
     * @expectedException \MonologModule\Exception\RuntimeException
     */
    public function testInstantiateGelfHandlerWithoutPort()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $gelfHandlerFactory = new GelfHandlerFactory(['host' => 'domain.com']);
        $gelfHandlerFactory->createService($serviceLocator);
    }
}
