<?php
namespace MonologModuleTest\Handler;

use Monolog\Handler\HandlerInterface;
use MonologModule\Handler\HandlerPluginManager;
use PHPUnit\Framework\TestCase;
use stdClass;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\ServiceLocatorInterface;

class HandlerPluginManagerTest extends TestCase
{
    public function testRetrievePlugin()
    {
        $handler = $this->createMock(HandlerInterface::class);

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $handlerPluginManager = new HandlerPluginManager($serviceLocator, [
            'services' => [
                HandlerInterface::class => $handler,
            ],
        ]);

        $handlerReturned = $handlerPluginManager->get(HandlerInterface::class);
        $this->assertSame($handler, $handlerReturned);
    }

    public function testValidateInvalidPlugin()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $handlerPluginManager = new HandlerPluginManager($serviceLocator);

        $this->expectException(InvalidServiceException::class);

        $handler = new stdClass;
        $handlerPluginManager->validatePlugin($handler);
    }
}
