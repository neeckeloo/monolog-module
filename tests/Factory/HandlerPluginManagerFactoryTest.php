<?php
namespace MonologModuleTest\Factory;

use MonologModule\Factory\HandlerPluginManagerFactory;
use MonologModule\Handler\HandlerPluginManager;
use Monolog\Handler\HandlerInterface;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class HandlerPluginManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveHandlerInstance()
    {
        $config = [
            'monolog' => [
                'handler_plugin_manager' => [
                    'services' => [
                        'foo' => $this->createMock(HandlerInterface::class)
                    ],
                ],
            ],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $handlerPluginManagerFactory = new HandlerPluginManagerFactory();

        $handlerPluginManager = $handlerPluginManagerFactory->createService($serviceLocator, 'foo');
        $this->assertInstanceOf(HandlerPluginManager::class, $handlerPluginManager);

        // Assert that a ServiceLocatorInterface service has been injected.
        // If it would be injected via the deprecated ServiceLocatorAwareInitializer, a E_USER_DEPRECATED would have been triggered.
        $this->assertInstanceOf('Zend\ServiceManager\ServiceLocatorInterface', $handlerPluginManager->getServiceLocator());

        $service = $handlerPluginManager->get('foo');
        $this->assertInstanceOf(HandlerInterface::class, $service);
    }
}
