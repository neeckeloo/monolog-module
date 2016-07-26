<?php
namespace MonologModuleTest\Factory;

use MonologModule\Factory\FormatterPluginManagerFactory;
use MonologModule\Formatter\FormatterPluginManager;
use Monolog\Formatter\FormatterInterface;
use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormatterPluginManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testRetrieveFormatterInstance()
    {
        $config = [
            'monolog' => [
                'formatter_plugin_manager' => [
                    'services' => [
                        'foo' => $this->createMock(FormatterInterface::class)
                    ],
                ],
            ],
        ];

        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $formatterPluginManagerFactory = new FormatterPluginManagerFactory();

        $formatterPluginManager = $formatterPluginManagerFactory->createService($serviceLocator, 'foo');
        $this->assertInstanceOf(FormatterPluginManager::class, $formatterPluginManager);

        $service = $formatterPluginManager->get('foo');
        $this->assertInstanceOf(FormatterInterface::class, $service);
    }
}
