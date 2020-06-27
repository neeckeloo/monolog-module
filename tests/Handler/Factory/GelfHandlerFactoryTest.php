<?php
namespace MonologModuleTest\Handler\Factory;

use MonologModule\Exception\RuntimeException;
use MonologModule\Handler\Factory\GelfHandlerFactory;
use Monolog\Handler\GelfHandler;
use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GelfHandlerFactoryTest extends TestCase
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

    public function testInstantiateGelfHandlerWithoutHost()
    {
        $this->expectException(RuntimeException::class);

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $gelfHandlerFactory = new GelfHandlerFactory(['port' => 123]);
        $gelfHandlerFactory->createService($serviceLocator);
    }

    public function testInstantiateGelfHandlerWithoutPort()
    {
        $this->expectException(RuntimeException::class);

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);

        $gelfHandlerFactory = new GelfHandlerFactory(['host' => 'domain.com']);
        $gelfHandlerFactory->createService($serviceLocator);
    }
}
