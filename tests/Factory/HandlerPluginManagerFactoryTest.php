<?php
namespace MonologModuleTest\Factory;

use MonologModule\Factory\HandlerPluginManagerFactory;
use PHPUnit_Framework_TestCase;

class HandlerPluginManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveHandlerInstance()
    {
        $config = [
            'monolog' => [
                'handler_plugin_manager' => [
                    'services' => [
                        'foo' => $this->getMock('Monolog\Handler\HandlerInterface')
                    ],
                ],
            ],
        ];

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $handlerPluginManagerFactory = new HandlerPluginManagerFactory();

        $handlerPluginManager = $handlerPluginManagerFactory->createService($serviceLocator);
        $this->assertInstanceOf('MonologModule\Handler\HandlerPluginManager', $handlerPluginManager);

        $service = $handlerPluginManager->get('foo');
        $this->assertInstanceOf('Monolog\Handler\HandlerInterface', $service);
    }
}
