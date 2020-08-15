<?php
namespace MonologModuleTest\Factory;

use MonologModule\Factory\HandlerPluginManagerFactory;
use MonologModule\Handler\HandlerPluginManager;
use Monolog\Handler\HandlerInterface;
use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceLocatorInterface;

class HandlerPluginManagerFactoryTest extends TestCase
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

        $handlerPluginManager = ($handlerPluginManagerFactory)($serviceLocator, 'foo');
        $this->assertInstanceOf(HandlerPluginManager::class, $handlerPluginManager);

        $service = $handlerPluginManager->get('foo');
        $this->assertInstanceOf(HandlerInterface::class, $service);
    }
}
