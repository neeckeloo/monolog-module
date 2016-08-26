<?php
namespace MonologModuleTest\Handler;

use Monolog\Handler\HandlerInterface;
use MonologModule\Handler\HandlerPluginManager;
use PHPUnit_Framework_TestCase;
use stdClass;
use Zend\ServiceManager\ServiceLocatorInterface;

class HandlerPluginManagerTest extends PHPUnit_Framework_TestCase
{
    public function testValidatePlugin()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $handlerPluginManager = new HandlerPluginManager($serviceLocator);

        $handler = $this->createMock(HandlerInterface::class);
        $handlerPluginManager->validatePlugin($handler);
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\InvalidServiceException
     */
    public function testValidateInvalidPlugin()
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $handlerPluginManager = new HandlerPluginManager($serviceLocator);

        $handler = new stdClass;
        $handlerPluginManager->validatePlugin($handler);
    }
}
