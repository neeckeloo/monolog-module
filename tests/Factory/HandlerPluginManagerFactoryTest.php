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

        // Assert that a ServiceLocatorInterface service has been injected.
        // If it would be injected via the deprecated ServiceLocatorAwareInitializer, a E_USER_DEPRECATED would have been triggered.
        $this->assertInstanceOf('Zend\ServiceManager\ServiceLocatorInterface', $formatterPluginManager->getServiceLocator());

        $service = $handlerPluginManager->get('foo');
        $this->assertInstanceOf('Monolog\Handler\HandlerInterface', $service);
    }
}
